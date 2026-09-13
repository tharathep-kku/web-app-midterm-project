<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReturnUnitController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [ReturnUnitController::class, 'dashboard'])->name('dashboard');
});

Route::get('/search', [ItemController::class, 'index'])->name('search.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/return-units/{returnUnit}', [ReturnUnitController::class, 'show'])->name('return-units.show');

require __DIR__.'/settings.php';
