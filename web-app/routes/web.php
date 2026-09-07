<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/search', [ItemController::class, 'index'])->name('search.index');

require __DIR__.'/settings.php';
