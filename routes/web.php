<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', \App\Livewire\SalesDashboard::class);
    Route::get('/inventory', \App\Livewire\InventoryManager::class);
    Route::get('/catalog', \App\Livewire\ProductManager::class);
    Route::get('/report', \App\Livewire\DailyReport::class);
});
