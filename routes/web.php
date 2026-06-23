<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Customer Auth (OTP flow) ─────────────────────────────────────
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('customer.guest')->group(function () {
        Route::get('/',          [AuthController::class, 'showStart'])      ->name('auth.start');
        Route::post('/',         [AuthController::class, 'processStart'])   ->name('auth.start');
        Route::get('/register',  [AuthController::class, 'showRegister'])   ->name('auth.register');
        Route::post('/register', [AuthController::class, 'processRegister'])->name('auth.register');
        Route::get('/verify',    [AuthController::class, 'showVerify'])     ->name('auth.verify');
        Route::post('/verify',   [AuthController::class, 'processVerify'])  ->name('auth.verify');
        Route::post('/resend',   [AuthController::class, 'resendOtp'])      ->name('auth.resend');
    });
    Route::middleware('customer.auth')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Admin Panel ──────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users',     UserController::class)->except(['show']);
    Route::get('activity',       [ActivityLogController::class, 'index'])->name('activity.index');

    // Customers (read + delete; OTP-based registration, no admin create)
    Route::get('customers',           [CustomerController::class, 'index'])  ->name('customers.index');
    Route::get('customers/{customer}',[CustomerController::class, 'show'])   ->name('customers.show');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Settings
    Route::get('settings',  [SettingController::class, 'index']) ->name('settings.index');
    Route::put('settings',  [SettingController::class, 'update'])->name('settings.update');
});

// ── User Profile ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
