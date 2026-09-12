<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', [ItemController::class, 'home'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/search', [ItemController::class, 'index'])->name('search.index');

Route::get('/archive', [ItemController::class, 'archive'])->name('archive.index');

require __DIR__.'/settings.php';
require __DIR__.'/agency_admin.php';
