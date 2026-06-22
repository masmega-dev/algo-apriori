<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::post('admin/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('admin/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('admin/orders/{order}/complete', [OrderController::class, 'complete'])->name('admin.orders.complete');
    Route::delete('admin/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::inertia('admin/reports', 'admin/reports')->name('admin.reports');
    Route::inertia('admin/settings', 'admin/settings')->name('admin.settings');
});

require __DIR__.'/settings.php';
