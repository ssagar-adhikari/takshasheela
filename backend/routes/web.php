<?php

use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::resource('users', UserController::class)->except('show');
    Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
