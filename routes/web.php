<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AdminAddonController;

// 1. Customer (Guest Checkout) Routes
Route::get('/', [CustomerController::class, 'index'])->name('home');
Route::get('/api/menu-version', function() {
    return response()->json(['version' => \App\Models\Setting::get('menu_last_updated', 0)]);
});
Route::get('/api/menus/stock', function() {
    return response()->json(
        \App\Models\Menu::select('id', 'name', 'is_available', 'stock')->get()
    );
});
Route::post('/checkout', [CustomerController::class, 'checkout'])->name('checkout.store');
Route::get('/order/{order_number}', [CustomerController::class, 'trackOrder'])->name('order.track');
Route::post('/order/{order_number}/received', [CustomerController::class, 'markReceived'])->name('order.received');
Route::post('/order/{order_number}/review', [CustomerController::class, 'submitReview'])->name('order.review');

// 2. Admin Authentication Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

// 3. Admin Protected Panel Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Store Settings (Operational & GPS Calibration)
    Route::post('/settings/operational', [AdminController::class, 'updateOperational'])->name('admin.settings.operational');
    Route::post('/settings/calibrate', [AdminController::class, 'calibrateLocation'])->name('admin.settings.calibrate');
    
    // Menu CRUD
    Route::resource('menus', AdminMenuController::class)->except(['show']);
    Route::post('menus/{menu}/toggle-stock', [AdminMenuController::class, 'toggleStock'])->name('menus.toggle-stock');
    
    // Topping Add-ons Management
    Route::resource('addons', AdminAddonController::class)->except(['create', 'edit', 'show']);
    
    // Order Validation & Status Steps
    Route::post('/orders/{order}/confirm-payment', [AdminController::class, 'confirmPayment'])->name('admin.orders.confirm-payment');
    Route::post('/orders/{order}/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
    Route::post('/orders/{order}/update-location', [AdminController::class, 'updateOrderLocation'])->name('admin.orders.update-location');

    // Notifications & Activity Logs
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications.index');
    Route::post('/notifications/{notification}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.read');
    Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.read-all');
});
