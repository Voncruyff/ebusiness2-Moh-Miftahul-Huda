<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// ✅ Halaman utama (welcome)
Route::get('/', function () {
    return view('welcome');
});

// ✅ Dashboard umum (opsional, bisa dipakai semua yang login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Halaman ADMIN (khusus admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin'); // ⬅️ Mengarah ke resources/views/admin.blade.php
    })->name('admin.dashboard');
});

// ✅ Halaman USER (khusus user)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user', function () {
        return view('user'); // ⬅️ Sekarang diarahkan ke user.blade.php
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
