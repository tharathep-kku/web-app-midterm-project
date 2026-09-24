<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReturnUnitController;

Route::get('/', [ItemController::class, 'home'])->name('home');

// จุดรับ-ส่งคืน: ล็อกอินแล้วเข้าได้เลย ไม่ต้องยืนยันอีเมล
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [ReturnUnitController::class, 'dashboard'])->name('dashboard');
});

Route::get('/search', [ItemController::class, 'search'])->name('search.home');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/return-units/{returnUnit}', [ReturnUnitController::class, 'show'])->name('return-units.show');

Route::get('/archive', [ItemController::class, 'archive'])->name('archive.home');

Route::get('/posts/create', [ItemController::class, 'create'])->name('posts.create');

Route::post('/posts', [ItemController::class, 'store'])->name('posts.store');

require __DIR__.'/settings.php';
require __DIR__.'/agency_admin.php';
