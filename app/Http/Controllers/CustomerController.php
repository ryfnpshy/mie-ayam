<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display the homepage with menus and store status.
     */
    public function index()
    {
        // Get store operational settings
        $storeStatus = Setting::get('store_status', 'open');
        $openTime = Setting::get('store_open_time', '08:00');
        $closeTime = Setting::get('store_close_time', '21:00');
        
        $now = now()->setTimezone('Asia/Jakarta');
        $currentTime = $now->format('H:i');
        
        $isOpenTime = ($currentTime >= $openTime && $currentTime <= $closeTime);
        $isStoreOpen = ($storeStatus === 'open' && $isOpenTime);

        // Fetch menus and calculate best sellers (top sold by quantity)
        $menus = Menu::with('reviews')->get();

        $bestSellerIds = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->take(2)
            ->pluck('menu_id')
            ->toArray();

        // If no orders yet, mark the first two menus as best sellers
        if (empty($bestSellerIds) && $menus->count() > 0) {
            $bestSellerIds = $menus->take(2)->pluck('id')->toArray();
        }

        // Fetch all available toppings (add-ons)
        $addons = AddOn::where('is_available', true)->get();

        // Get store coordinates for the GPS script
        $storeLat = Setting::get('store_latitude', '-6.200000');
        $storeLng = Setting::get('store_longitude', '106.816666');

        return view('customer.index', compact('menus', 'addons', 'isStoreOpen', 'openTime', 'closeTime', 'bestSellerIds', 'storeLat', 'storeLng'));
    }

    /**
     * Submit an order (Guest Checkout).
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_wa' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'distance_km' => 'required|numeric|min:0',
            'cart_items' => 'required|json',
        ]);

        $cartItems = json_decode($request->cart_items, true);
        if (empty($cartItems)) {
            return back()->withErrors(['cart_items' => 'Keranjang belanja tidak boleh kosong.']);
        }

        DB::beginTransaction();
        try {
            // Calculate shipping cost: Distance (KM) * Rp5.000
            $shippingRate = (int) Setting::get('shipping_rate_per_km', 5000);
            $distance = (float) $request->distance_km;
            $shippingCost = (int) ceil($distance * $shippingRate);

            // Compute total item price
            $itemsPrice = 0;
            $preparedItems = [];

            foreach ($cartItems as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                if (!$menu->is_available || $menu->stock < $item['quantity']) {
                    throw new \Exception("Stok menu {$menu->name} tidak mencukupi (Tersedia: {$menu->stock}).");
                }

                $itemTotal = $menu->price * $item['quantity'];

                // Toppings cost
                $toppingIds = $item['toppings'] ?? [];
                $toppingsPriceSum = 0;
                $addons = [];
                if (!empty($toppingIds)) {
                    $dbToppings = AddOn::whereIn('id', $toppingIds)->where('is_available', true)->get();
                    foreach ($dbToppings as $addon) {
                        $toppingsPriceSum += $addon->price;
                        $addons[] = $addon->id;
                    }
                }

                $itemTotal += ($toppingsPriceSum * $item['quantity']);
                $itemsPrice += $itemTotal;

                $preparedItems[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'spiciness_level' => $menu->is_spicy_variant_enabled ? ($item['spiciness_level'] ?? 0) : null,
                    'notes' => $item['notes'] ?? null,
                    'addons' => $addons
                ];
            }

            $totalPrice = $itemsPrice + $shippingCost;
            $orderNumber = 'BAK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_wa' => $request->customer_wa,
                'customer_address' => $request->customer_address,
                'distance_km' => $distance,
                'shipping_cost' => $shippingCost,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_proof_status' => 'unpaid',
            ]);

            // Create Order Items and attach Addons
            foreach ($preparedItems as $pItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $pItem['menu_id'],
                    'quantity' => $pItem['quantity'],
                    'spiciness_level' => $pItem['spiciness_level'],
                    'notes' => $pItem['notes'],
                ]);

                if (!empty($pItem['addons'])) {
                    $orderItem->addOns()->attach($pItem['addons']);
                }
            }

            DB::commit();

            // Broadcast real-time order submission for admin
            try {
                event(new \App\Events\OrderCreated($order));
            } catch (\Exception $e) {
                // Keep checkout working even if broadcasting server is down
            }

            return redirect()->route('order.track', $orderNumber)->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['checkout_error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show tracking page.
     */
    public function trackOrder(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items.menu', 'items.addOns'])->firstOrFail();
        return view('customer.track', compact('order'));
    }

    /**
     * Customer marks order as received.
     */
    public function markReceived(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        if ($order->status === 'shipped') {
            $order->update(['status' => 'completed']);

            // Create Admin Notification
            $confTime = now()->setTimezone('Asia/Jakarta');
            \App\Models\AdminNotification::create([
                'order_id' => $order->id,
                'title' => 'Pesanan Diterima Pelanggan',
                'message' => "Pesanan #{$order->order_number} telah dikonfirmasi selesai oleh {$order->customer_name}.",
                'type' => 'order_confirmed',
                'is_read' => false,
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'confirmation_time' => $confTime->toDateTimeString(),
                ],
            ]);

            // Create Activity Log
            \App\Models\ActivityLog::log(
                'notification_sent',
                "Mengirimkan notifikasi pesanan #{$order->order_number} selesai dikonfirmasi oleh {$order->customer_name}.",
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'confirmation_time' => $confTime->toDateTimeString(),
                ]
            );

            // Broadcast to admin dashboard about order completion
            try {
                event(new \App\Events\OrderStatusUpdated($order));
            } catch (\Exception $e) {
                // Fail gracefully
            }

            return back()->with('success', 'Pesanan telah selesai! Silakan berikan ulasan.');
        }

        return back()->withErrors(['error' => 'Pesanan belum dikirim oleh penjual.']);
    }

    /**
     * Submit review.
     */
    public function submitReview(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        if ($order->status !== 'completed') {
            return back()->withErrors(['error' => 'Anda hanya bisa memberikan ulasan setelah makanan sampai.']);
        }

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
            'comments' => 'array',
            'comments.*' => 'nullable|string|max:500',
        ]);

        $reviewsSummary = [];
        $hasNewReview = false;

        foreach ($request->ratings as $menuId => $rating) {
            // Check if already reviewed for this order/menu
            $exists = Review::where('order_id', $order->id)->where('menu_id', $menuId)->exists();
            if (!$exists) {
                $menu = Menu::find($menuId);
                $comment = $request->comments[$menuId] ?? null;
                Review::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);

                $reviewsSummary[] = [
                    'menu_name' => $menu ? $menu->name : "Menu ID #$menuId",
                    'rating' => (int) $rating,
                    'comment' => $comment,
                ];
                $hasNewReview = true;
            }
        }

        if ($hasNewReview) {
            $confirmationTime = $order->updated_at ? $order->updated_at->setTimezone('Asia/Jakarta')->toDateTimeString() : now()->setTimezone('Asia/Jakarta')->toDateTimeString();

            // Create Admin Notification for Review Submission
            \App\Models\AdminNotification::create([
                'order_id' => $order->id,
                'title' => 'Ulasan Baru Diterima',
                'message' => "Pelanggan {$order->customer_name} memberikan ulasan untuk pesanan #{$order->order_number}.",
                'type' => 'review_submitted',
                'is_read' => false,
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'confirmation_time' => $confirmationTime,
                    'reviews' => $reviewsSummary,
                ],
            ]);

            // Create Activity Log
            \App\Models\ActivityLog::log(
                'notification_sent',
                "Mengirimkan notifikasi ulasan baru untuk pesanan #{$order->order_number} dari {$order->customer_name}.",
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'confirmation_time' => $confirmationTime,
                    'reviews' => $reviewsSummary,
                ]
            );
        }

        return redirect()->route('home')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
