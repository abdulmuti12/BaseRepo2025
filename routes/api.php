<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Customer\AuthCustomerController;
use App\Http\Middleware\CanAccessMenu;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/check-route', fn() => 'ok');

Route::prefix('admins')->group(function () {
    Route::post('/logincheck', [AuthController::class, 'login'])->name('logincheck');
    Route::get('/check-route', fn() => 'ok')->name('login');


    Route::middleware(['jwt.auth'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::apiResource('admin', AdminController::class);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Menggunakan middleware dengan parameter untuk mengecek akses menu
        Route::middleware([CanAccessMenu::class . ':Dashboard'])->get('/dashboard', function () {
            return response()->json(['message' => 'Welcome to Dashboard']);
        });

        Route::middleware([CanAccessMenu::class . ':Users'])->get('/users', function () {
            return response()->json(['message' => 'Users Page']);
        });

        Route::middleware([CanAccessMenu::class . ':Settings'])->get('/settings', function () {
            return response()->json(['message' => 'Settings Page']);
        });
    });
});

Route::prefix('customers')->group(function () {
    Route::post('/login-customer', [AuthCustomerController::class, 'login'])->name('login-customer');
    Route::get('/check-route', fn() => 'ok')->name('login');

});