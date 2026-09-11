<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

// ===== Authentication (internal restoran) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:login');

// Mengembalikan token CSRF sesi saat ini (untuk refresh token di halaman login
// yang di-restore dari bfcache saat tombol Back ditekan).
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Register publik dihapus — akun hanya dibuat oleh Admin (Manajemen Pengguna).
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Dashboard internal restoran
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KasirController;

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', [KasirController::class, 'home'])->name('kasir.dashboard');
    Route::get('/kasir/dashboard/orders-data', [KasirController::class, 'pendingOrdersData'])->name('kasir.dashboard.orders-data');
    Route::get('/kasir/orders', [KasirController::class, 'orders'])->name('kasir.orders');
    Route::get('/kasir/orders/{order}', [KasirController::class, 'orderDetail'])->name('kasir.orders.show');
    Route::patch('/kasir/orders/{order}/status', [KasirController::class, 'updateStatus'])->name('kasir.orders.status');
    Route::patch('/kasir/orders/{order}/payment/confirm', [KasirController::class, 'confirmPayment'])->name('kasir.payment.confirm');
    Route::get('/kasir/payments', [KasirController::class, 'payments'])->name('kasir.payments');
    Route::get('/kasir/notifications', [KasirController::class, 'notifications'])->name('kasir.notifications');
    Route::get('/kasir/notifications/data', [KasirController::class, 'notificationsData'])->name('kasir.notifications.data');
    Route::patch('/kasir/notifications/{notification}/read', [KasirController::class, 'notificationsMarkRead'])->name('kasir.notifications.read');
    Route::post('/kasir/notifications/read-all', [KasirController::class, 'notificationsMarkAllRead'])->name('kasir.notifications.readall');
    Route::get('/kasir/tables', [KasirController::class, 'tables'])->name('kasir.tables');
    Route::patch('/kasir/tables/{table}/status', [KasirController::class, 'tableStatus'])->name('kasir.tables.status');
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'home'])->name('dashboard');
    Route::get('/menu', [AdminController::class, 'menuIndex'])->name('menu');
    Route::get('/menu/create', [AdminController::class, 'menuCreate'])->name('menu.create');
    Route::post('/menu', [AdminController::class, 'menuStore'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [AdminController::class, 'menuEdit'])->name('menu.edit');
    Route::put('/menu/{menu}', [AdminController::class, 'menuUpdate'])->name('menu.update');
    Route::delete('/menu/{menu}', [AdminController::class, 'menuDestroy'])->name('menu.destroy');
    Route::get('/users', [AdminController::class, 'userIndex'])->name('users');
    Route::post('/users', [AdminController::class, 'userStore'])->name('users.store');
    Route::patch('/users/{user}', [AdminController::class, 'userUpdate'])->name('users.update');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'userToggle'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('users.destroy');
    Route::get('/tables', [AdminController::class, 'tableQr'])->name('tables');
    Route::post('/tables', [AdminController::class, 'tableStore'])->name('tables.store');
    Route::get('/tables/{table}/qr', [AdminController::class, 'tableQrDownload'])->name('tables.qr');
    Route::delete('/tables/{table}', [AdminController::class, 'tableDestroy'])->name('tables.destroy');
});

Route::get('/', function () {
    // Landing tanpa token: customer diarahkan scan QR meja. (Tidak membocorkan token meja.)
    return view('customer.table-status', ['message' => 'Silakan pindai QR Code pada meja Anda untuk mulai memesan.']);
});

Route::get('/qr-test', [MenuController::class, 'qrTest'])->name('menu.qr-test');

Route::get('/order/{table:qr_token}', [MenuController::class, 'index'])->name('menu')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
Route::post('/order/{table:qr_token}/payment', [OrderController::class, 'payment'])->name('order.payment')->middleware([App\Http\Middleware\EnsureCustomerSession::class, 'throttle:order-create']);
Route::get('/order/{table:qr_token}/payment', [OrderController::class, 'paymentSelect'])->name('order.payment.select')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
Route::get('/order/{table:qr_token}/payment/qris', [OrderController::class, 'qris'])->name('order.payment.qris')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
Route::get('/order/{table:qr_token}/payment/cash', [OrderController::class, 'cash'])->name('order.payment.cash')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
Route::post('/order/{table:qr_token}/confirm', [OrderController::class, 'confirm'])->name('order.confirm')->middleware([App\Http\Middleware\EnsureCustomerSession::class, 'throttle:order-create']);
Route::get('/order/{table:qr_token}/status/{order}', [OrderController::class, 'status'])->name('order.status')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
Route::get('/order/{table:qr_token}/status/{order}/poll', [OrderController::class, 'pollStatus'])->name('order.poll')->middleware(App\Http\Middleware\EnsureCustomerSession::class);
