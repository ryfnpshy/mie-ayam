<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Menu;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BakmiOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic operational settings
        Setting::create(['key' => 'store_status', 'value' => 'open']);
        Setting::create(['key' => 'store_open_time', 'value' => '08:00']);
        Setting::create(['key' => 'store_close_time', 'value' => '21:00']);
        Setting::create(['key' => 'store_latitude', 'value' => '-6.200000']);
        Setting::create(['key' => 'store_longitude', 'value' => '106.816666']);
        Setting::create(['key' => 'shipping_rate_per_km', 'value' => '5000']);

        // Seed menus
        $this->menu = Menu::create([
            'name' => 'Mie Chili Oil',
            'description' => 'Pedas nampol',
            'price' => 18000,
            'is_available' => true,
            'is_spicy_variant_enabled' => true,
        ]);

        // Seed toppings
        $this->addon = AddOn::create([
            'name' => 'Pangsit',
            'price' => 3000,
            'is_available' => true,
        ]);

        // Seed admin
        $this->admin = Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }

    /**
     * Test guest customer visiting the homepage.
     */
    public function test_customer_can_visit_homepage(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Bakmi Ayam Kembar');
        $response->assertSee('Mie Chili Oil');
    }

    /**
     * Test checkout order creation, status flow, and admin actions.
     */
    public function test_complete_order_and_admin_workflow(): void
    {
        // 1. Submit Guest Order via Checkout
        $cartData = [
            [
                'menu_id' => $this->menu->id,
                'quantity' => 2,
                'spiciness_level' => 3,
                'notes' => 'Tolong daun bawang dipisah',
                'toppings' => [$this->addon->id],
                'toppings_details' => [
                    ['id' => $this->addon->id, 'name' => 'Pangsit', 'price' => 3000]
                ]
            ]
        ];

        $checkoutResponse = $this->post('/checkout', [
            'customer_name' => 'Budi',
            'customer_wa' => '0812345678',
            'customer_address' => 'Jl. Sudirman No 12, Jakarta',
            'distance_km' => 2.50, // 2.50 KM * Rp5.000 = Rp12.500 shipping
            'cart_items' => json_encode($cartData),
        ]);

        // Should create the order and redirect to tracking page
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('Budi', $order->customer_name);
        $this->assertEquals(12500, $order->shipping_cost);
        
        // Total = (18000 + 3000) * 2 + 12500 = 42000 + 12500 = 54500
        $this->assertEquals(54500, $order->total_price);
        $this->assertEquals('pending', $order->status);

        $checkoutResponse->assertRedirect(route('order.track', $order->order_number));

        // 2. Load tracking page
        $trackResponse = $this->get(route('order.track', $order->order_number));
        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Budi');
        $trackResponse->assertSee('Level 3');

        // 3. Admin Logs In
        $loginResponse = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);
        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        // 4. Admin Confirms Payment
        $confirmResponse = $this->actingAs($this->admin)
            ->post(route('admin.orders.confirm-payment', $order->id));
        
        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals('paid', $order->payment_proof_status);

        // 5. Admin updates status to processing and then shipped
        $this->actingAs($this->admin)
            ->post(route('admin.orders.update-status', $order->id), [
                'status' => 'processing'
            ]);
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        $this->actingAs($this->admin)
            ->post(route('admin.orders.update-status', $order->id), [
                'status' => 'shipped'
            ]);
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        // 6. Customer marks order as received
        $receivedResponse = $this->post(route('order.received', $order->order_number));
        $order->refresh();
        $this->assertEquals('completed', $order->status);

        // Assert Order Confirmed Notification & Activity Log generated
        $this->assertDatabaseHas('admin_notifications', [
            'order_id' => $order->id,
            'type' => 'order_confirmed',
            'is_read' => false,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'activity_type' => 'notification_sent',
        ]);

        // 7. Customer submits review
        $reviewResponse = $this->post(route('order.review', $order->order_number), [
            'ratings' => [
                $this->menu->id => 5
            ],
            'comments' => [
                $this->menu->id => 'Sangat enak, mie kenyal dan chili oil pedas mantap!'
            ]
        ]);

        $reviewResponse->assertRedirect(route('home'));
        
        // Assert review saved
        $this->assertDatabaseHas('reviews', [
            'menu_id' => $this->menu->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Sangat enak, mie kenyal dan chili oil pedas mantap!',
        ]);

        // Assert Review Notification generated with exact details
        $this->assertDatabaseHas('admin_notifications', [
            'order_id' => $order->id,
            'type' => 'review_submitted',
            'is_read' => false,
        ]);

        $reviewNotif = \App\Models\AdminNotification::where('type', 'review_submitted')->first();
        $this->assertNotNull($reviewNotif);
        $this->assertEquals($order->customer_name, $reviewNotif->data['customer_name']);
        $this->assertEquals('Sangat enak, mie kenyal dan chili oil pedas mantap!', $reviewNotif->data['reviews'][0]['comment']);
        $this->assertEquals(5, $reviewNotif->data['reviews'][0]['rating']);

        // Assert Notification Page & Actions for Admin
        $notifPageResponse = $this->actingAs($this->admin)
            ->get(route('admin.notifications.index'));
        $notifPageResponse->assertStatus(200);
        $notifPageResponse->assertSee('Pusat Notifikasi');
        $notifPageResponse->assertSee('Sangat enak, mie kenyal dan chili oil pedas mantap!');

        // Mark notification as read
        $readResponse = $this->actingAs($this->admin)
            ->post(route('admin.notifications.read', $reviewNotif->id));
        $readResponse->assertRedirect();
        
        $reviewNotif->refresh();
        $this->assertTrue($reviewNotif->is_read);

        // Assert mark all as read
        $readAllResponse = $this->actingAs($this->admin)
            ->post(route('admin.notifications.read-all'));
        $readAllResponse->assertRedirect();
        $this->assertEquals(0, \App\Models\AdminNotification::where('is_read', false)->count());
    }

    /**
     * Test menu stock management workflow.
     */
    public function test_menu_stock_management(): void
    {
        // Set menu stock to 5
        $this->menu->update(['stock' => 5, 'is_available' => true]);

        // 1. Checkout with quantity exceeding stock (6) should fail
        $cartDataExceed = [
            [
                'menu_id' => $this->menu->id,
                'quantity' => 6,
                'toppings' => [],
            ]
        ];

        $checkoutExceedResponse = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'customer_wa' => '0812345678',
            'customer_address' => 'Jl. Merdeka 10',
            'distance_km' => 1.0,
            'cart_items' => json_encode($cartDataExceed),
        ]);

        $checkoutExceedResponse->assertSessionHasErrors();
        $this->assertEquals(0, Order::count()); // No order created

        // 2. Checkout with quantity within stock (3) should succeed
        $cartDataOk = [
            [
                'menu_id' => $this->menu->id,
                'quantity' => 3,
                'toppings' => [],
            ]
        ];

        $checkoutOkResponse = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'customer_wa' => '0812345678',
            'customer_address' => 'Jl. Merdeka 10',
            'distance_km' => 1.0,
            'cart_items' => json_encode($cartDataOk),
        ]);

        $checkoutOkResponse->assertSessionHasNoErrors();
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);

        // Menu stock should still be 5 because payment is not confirmed yet
        $this->menu->refresh();
        $this->assertEquals(5, $this->menu->stock);
        $this->assertTrue($this->menu->is_available);

        // 3. Confirm Payment (marks order as paid/confirmed and decrements stock)
        $this->actingAs($this->admin)
            ->post(route('admin.orders.confirm-payment', $order->id));

        $this->menu->refresh();
        $this->assertEquals(2, $this->menu->stock); // 5 - 3 = 2
        $this->assertTrue($this->menu->is_available);

        // 4. Create another order with remaining stock (2)
        $cartDataRemaining = [
            [
                'menu_id' => $this->menu->id,
                'quantity' => 2,
                'toppings' => [],
            ]
        ];

        $this->post('/checkout', [
            'customer_name' => 'Jane Doe',
            'customer_wa' => '0898765432',
            'customer_address' => 'Jl. Kartini 5',
            'distance_km' => 1.0,
            'cart_items' => json_encode($cartDataRemaining),
        ]);

        $order2 = Order::orderBy('id', 'desc')->first();
        
        // Confirm payment of order 2 (should drop stock to 0 and set is_available to false)
        $this->actingAs($this->admin)
            ->post(route('admin.orders.confirm-payment', $order2->id));

        $this->menu->refresh();
        $this->assertEquals(0, $this->menu->stock);
        $this->assertFalse($this->menu->is_available); // Out of stock -> disabled
    }
}
