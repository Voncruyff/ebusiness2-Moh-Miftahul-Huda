<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;

// ✅ Halaman utama (welcome)
Route::get('/', function () {
    return view('welcome');
});

// ✅ Redirect otomatis ketika membuka /dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect('/login');
    }

    // Cek role berdasarkan kolom 'role' di tabel users
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'user') {
        return redirect()->route('user.dashboard');
    }

    return abort(403, 'Unauthorized');
})->middleware(['auth'])->name('dashboard');

// ✅ Halaman ADMIN (khusus admin)
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/admin', function () {
        return view('dashboard.admin'); // resources/views/dashboard/admin.blade.php
    })->name('admin.dashboard');

    // Transaksi
    Route::get('/admin/transactions', function () {
        return view('fitur.transactions');
    })->name('admin.transactions');

    // Produk - Resource Route
    Route::resource('admin/products', ProductController::class)->names([
        'index' => 'admin.products',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);

    // Pelanggan
    Route::get('/admin/customers', function () {
        return view('fitur.customers');
    })->name('admin.customers');

    // Laporan
    Route::get('/admin/reports', function () {
        return view('fitur.reports');
    })->name('admin.reports');

    // Inventory
    Route::get('/admin/inventory', function () {
        return view('fitur.inventory');
    })->name('admin.inventory');

    // Pengaturan
    Route::get('/admin/settings', function () {
        return view('fitur.settings');
    })->name('admin.settings');
});

// ✅ Halaman USER (khusus user)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user', function () {
        return view('dashboard.user'); // resources/views/dashboard/user.blade.php
    })->name('user.dashboard');
});

// ✅ Halaman profil (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Route bawaan auth (login/register)
require __DIR__.'/auth.php';
