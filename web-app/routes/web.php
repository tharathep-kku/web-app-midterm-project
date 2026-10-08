<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReturnUnitController;
use App\Http\Controllers\UserPostController;

Route::get('/', [ItemController::class, 'home'])->name('home');

// จุดรับ-ส่งคืน: ล็อกอินแล้วเข้าได้เลย ไม่ต้องยืนยันอีเมล
Route::middleware('auth')->group(function () {
    Route::get('location', [ReturnUnitController::class, 'index'])->name('location');
});

Route::get('/search', [ItemController::class, 'search'])->name('search.home');
Route::delete('/search/history', [ItemController::class, 'clearHistory'])->name('search.history.clear');
Route::delete('/search/history/{index}', [ItemController::class, 'clearHistoryItem'])->whereNumber('index')->name('search.history.clear-one');
Route::middleware('auth')->get('/items/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/return-units/{returnUnit}', [ReturnUnitController::class, 'show'])->name('return-units.show');

Route::get('/archive', [ItemController::class, 'archive'])->name('archive.home');

Route::get('/posts/create', [ItemController::class, 'create'])->name('posts.create');

Route::post('/posts', [ItemController::class, 'store'])->name('posts.store');

# เพิ่มใหม่
Route::middleware('auth')->group(function () {
    Route::get('/posts/{id}/edit', [UserPostController::class, 'edit'])->whereNumber('id')->name('posts.edit');
    Route::put('/posts/{id}', [UserPostController::class, 'update'])->whereNumber('id')->name('posts.update');
    Route::delete('/posts/{id}', [UserPostController::class, 'destroy'])->whereNumber('id')->name('posts.destroy');
    Route::put('/posts/{id}/evidence', [UserPostController::class, 'submitEvidence'])->whereNumber('id')->name('posts.evidence');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
