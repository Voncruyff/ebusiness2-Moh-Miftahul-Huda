<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminHistoryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AdminUserController; // ✅ BALIKIN

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HistoryController;

use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

// ✅ jangan hapus apapun: route ini kita "pertahankan idenya"
// Tapi biar link utama gak nyangkut, kita bikin / aman:
// - belum login -> login
// - sudah login -> dashboard (role based)
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
})->name('home');

// ✅ dashboard role based (punyamu, dibuat lebih aman)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    $role = $user->role ?? 'user';

    return $role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');


// ==========================================================
// ADMIN (auth + role:admin)
// ==========================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/history', [AdminHistoryController::class, 'index'])->name('history');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');

        // ✅ Manage User (BALIK)
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');

        // ✅ Inventory
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');

        // ✅ HTMX partial detail (no kedip)
        Route::get('/inventory/detail/{id}', [AdminInventoryController::class, 'detail'])->name('inventory.detail');

        // ✅ Restock
        Route::post('/inventory/{product}/restock', [AdminInventoryController::class, 'restock'])->name('inventory.restock');

        // Pages lain (statis)
        Route::view('/transactions', 'fitur.transactions')->name('transactions');
        Route::view('/customers', 'fitur.customers')->name('customers');
        Route::view('/settings', 'fitur.settings')->name('settings');

        // Produk CRUD
        Route::resource('products', ProductController::class)->except(['create', 'edit']);
    });


// ==========================================================
// USER (auth + role:user)
// ==========================================================
Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/user', function () {
        return view('dashboardUser.user');
    })->name('user.dashboard');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/keranjang/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/keranjang/remove', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/history', [HistoryController::class, 'index'])->name('user.history');

    Route::get('/stream-products', [ProductController::class, 'streamProducts'])->name('user.stream-products');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/pay', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
});


// ==========================================================
// SSE LEGACY (kalau masih dipakai; kalau tidak, hapus)
// ==========================================================
Route::get('/sse/products', function () {

    return new StreamedResponse(function () {
        while (true) {
            $products = Product::latest()->get();
            echo "data: " . json_encode($products) . "\n\n";
            @ob_flush();
            @flush();
            sleep(2);
        }
    }, 200, [
        'Content-Type'  => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection'    => 'keep-alive',
    ]);

})->middleware(['auth', 'role:user'])->name('user.sse-products');


// ==========================================================
// Profile (Breeze)
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
