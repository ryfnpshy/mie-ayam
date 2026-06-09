<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the administrator dashboard with analytics, orders, settings, etc.
     */
    public function dashboard()
    {
        // 1. Calculate Daily Profit (Completed/paid/processing/shipped orders today)
        $dailyProfit = Order::whereDate('created_at', today())
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->sum('total_price');

        // 2. Best Selling Menus
        $bestSellers = Menu::select('menus.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->leftJoin('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->groupBy('menus.id', 'menus.name', 'menus.description', 'menus.image_path', 'menus.price', 'menus.stock', 'menus.is_available', 'menus.is_spicy_variant_enabled', 'menus.created_at', 'menus.updated_at')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 3. Slow Moving Menus
        $slowMoving = Menu::select('menus.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->leftJoin('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->groupBy('menus.id', 'menus.name', 'menus.description', 'menus.image_path', 'menus.price', 'menus.stock', 'menus.is_available', 'menus.is_spicy_variant_enabled', 'menus.created_at', 'menus.updated_at')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();

        // 4. Highest Rated Menus
        $highestRated = Menu::select('menus.*')
            ->selectRaw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
            ->leftJoin('reviews', 'menus.id', '=', 'reviews.menu_id')
            ->groupBy('menus.id', 'menus.name', 'menus.description', 'menus.image_path', 'menus.price', 'menus.stock', 'menus.is_available', 'menus.is_spicy_variant_enabled', 'menus.created_at', 'menus.updated_at')
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        // 5a. Low stock warning (stock <= 5 and still available)
        $lowStockMenus = Menu::where('is_available', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        // 6. Recent Reviews
        $reviews = Review::with(['menu', 'order'])->latest()->take(10)->get();

        // 7. Orders list grouped by status pipeline
        $orders = Order::with(['items.menu', 'items.addOns'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 8. Store parameters
        $storeStatus = Setting::get('store_status', 'open');
        $openTime = Setting::get('store_open_time', '08:00');
        $closeTime = Setting::get('store_close_time', '21:00');
        $storeLat = Setting::get('store_latitude', '-6.200000');
        $storeLng = Setting::get('store_longitude', '106.816666');

        return view('admin.dashboard', compact(
            'dailyProfit', 'bestSellers', 'slowMoving', 'highestRated',
            'reviews', 'orders', 'storeStatus', 'openTime', 'closeTime',
            'storeLat', 'storeLng', 'lowStockMenus'
        ));
    }

    /**
     * Update store operational details.
     */
    public function updateOperational(Request $request)
    {
        $request->validate([
            'store_status' => 'required|in:open,closed',
            'store_open_time' => 'required|date_format:H:i',
            'store_close_time' => 'required|date_format:H:i',
        ]);

        Setting::set('store_status', $request->store_status);
        Setting::set('store_open_time', $request->store_open_time);
        Setting::set('store_close_time', $request->store_close_time);

        return back()->with('success', 'Pengaturan operasional berhasil diperbarui.');
    }

    /**
     * Calibrate physical store GPS coordinates.
     */
    public function calibrateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Setting::set('store_latitude', $request->latitude);
        Setting::set('store_longitude', $request->longitude);

        return response()->json([
            'success' => true,
            'message' => 'Koordinat toko berhasil dikalibrasi.',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }

    /**
     * Confirm QRIS payment for an order.
     */
    public function confirmPayment(Order $order)
    {
        if ($order->status === 'pending') {
            DB::beginTransaction();
            try {
                // Deduct stocks
                foreach ($order->items as $item) {
                    $menu = $item->menu;
                    if ($menu) {
                        $newStock = max(0, $menu->stock - $item->quantity);
                        $menu->update([
                            'stock' => $newStock,
                            'is_available' => $newStock > 0 ? $menu->is_available : false,
                        ]);
                    }
                }

                $order->update([
                    'status' => 'paid',
                    'payment_proof_status' => 'paid'
                ]);

                \App\Models\Setting::touchMenuVersion();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Gagal memproses konfirmasi pembayaran: ' . $e->getMessage()]);
            }

            try {
                event(new \App\Events\OrderStatusUpdated($order));
            } catch (\Exception $e) {
                // Fail-safe
            }

            return back()->with('success', 'Pembayaran untuk pesanan ' . $order->order_number . ' telah dikonfirmasi.');
        }

        return back()->withErrors(['error' => 'Pesanan ini tidak berada dalam status menunggu konfirmasi pembayaran.']);
    }

    /**
     * Move order to next step in the pipeline.
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,completed',
        ]);

        $order->update(['status' => $request->status]);

        try {
            event(new \App\Events\OrderStatusUpdated($order));
        } catch (\Exception $e) {
            // Fail-safe
        }

        return back()->with('success', 'Status pesanan ' . $order->order_number . ' diperbarui menjadi: ' . ucfirst($request->status));
    }

    /**
     * Show notifications index and activity logs list.
     */
    public function notifications()
    {
        $notifications = \App\Models\AdminNotification::latest()->get();
        $activityLogs = \App\Models\ActivityLog::latest()->take(100)->get();

        return view('admin.notifications.index', compact('notifications', 'activityLogs'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markNotificationRead(\App\Models\AdminNotification $notification)
    {
        $notification->update(['is_read' => true]);

        \App\Models\ActivityLog::log('action_performed', "Menandai notifikasi #{$notification->id} sebagai dibaca.");

        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Update order delivery location (flexible destination).
     */
    public function updateOrderLocation(Request $request, Order $order)
    {
        $request->validate([
            'customer_address' => 'required|string|max:500',
            'distance_km' => 'required|numeric|min:0',
        ]);

        // Calculate new shipping cost
        $shippingRate = (int) Setting::get('shipping_rate_per_km', 5000);
        $newShippingCost = (int) ceil($request->distance_km * $shippingRate);

        // Calculate current items total (total_price - old_shipping_cost)
        $itemsTotal = $order->total_price - $order->shipping_cost;
        $newTotalPrice = $itemsTotal + $newShippingCost;

        $order->update([
            'customer_address' => $request->customer_address,
            'distance_km' => $request->distance_km,
            'shipping_cost' => $newShippingCost,
            'total_price' => $newTotalPrice,
        ]);

        try {
            event(new \App\Events\OrderStatusUpdated($order));
        } catch (\Exception $e) {}

        return back()->with('success', 'Lokasi pengiriman pesanan #' . $order->order_number . ' berhasil diperbarui.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        \App\Models\AdminNotification::where('is_read', false)->update(['is_read' => true]);

        \App\Models\ActivityLog::log('action_performed', "Menandai seluruh notifikasi sebagai dibaca.");

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
