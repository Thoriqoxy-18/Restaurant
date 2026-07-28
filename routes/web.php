<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenu;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\RestaurantTableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\OrderController as KasirOrder;
use App\Http\Controllers\Kasir\PaymentController as KasirPayment;

// ===== Customer Routes =====
Route::get('/order/{table:qr_token}', [ScanController::class, 'orderPage'])->name('customer.order');
Route::post('/customer/session', [ScanController::class, 'initSession'])->name('customer.session');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('menu.detail');

Route::post('/cart/add', [CartController::class, 'add'])->middleware('customer.session')->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->middleware('customer.session')->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->middleware('customer.session')->name('cart.remove');
Route::get('/checkout', [OrderController::class, 'checkoutPage'])->middleware('customer.session')->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->middleware('customer.session')->name('checkout.store');
Route::get('/orders', [OrderController::class, 'index'])->middleware('customer.session')->name('customer.orders');
Route::get('/orders/{order}', [OrderController::class, 'detail'])->middleware('customer.session')->name('customer.order.detail');

// ===== Staff Auth =====
Route::prefix('/staff')->name('staff.')->group(function () {
    Route::get('/login', [AdminAuth::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminAuth::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');
});

// ===== Kasir Dashboard =====
Route::prefix('/kasir')->name('kasir.')->middleware(['auth', 'role:kasir,owner'])->group(function () {
    Route::get('/', [KasirDashboard::class, 'index'])->name('dashboard');
    Route::get('/orders', [KasirOrder::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [KasirOrder::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [KasirOrder::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/payment', [KasirPayment::class, 'process'])->name('orders.payment');
});

// ===== Owner Dashboard =====
Route::prefix('/admin')->name('admin.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('menus', AdminMenu::class);
    Route::resource('tables', RestaurantTableController::class);
    Route::get('/orders', [AdminOrder::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrder::class, 'show'])->name('orders.show');
    Route::resource('staff', UserController::class)->parameters(['staff' => 'user']);
});
