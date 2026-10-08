<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReturnUnitController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\AdminController;

Route::get('/', [ItemController::class, 'home'])->name('home');
Route::get('/archive', [ItemController::class, 'archive'])->name('archive.home');

Route::get('/search', [ItemController::class, 'search'])->name('search.home');
Route::delete('/search/history', [ItemController::class, 'clearHistory'])->name('search.history.clear');
Route::delete('/search/history/{index}', [ItemController::class, 'clearHistoryItem'])->whereNumber('index')->name('search.history.clear-one');
Route::middleware('auth')->get('/items/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/return-units/{returnUnit}', [ReturnUnitController::class, 'show'])->name('return-units.show');

Route::post('/posts', [ItemController::class, 'store'])->name('posts.store');
Route::get('/posts/create', [ItemController::class, 'create'])->name('posts.create');

// จุดรับ-ส่งคืน: ล็อกอินแล้วเข้าได้เลย ไม่ต้องยืนยันอีเมล
Route::middleware('auth')->group(function () {
    Route::get('location', [ReturnUnitController::class, 'index'])->name('location');
});

# เพิ่มใหม่
Route::middleware('auth')->group(function () {
    Route::get('/posts/{id}/edit', [UserPostController::class, 'edit'])->whereNumber('id')->name('posts.edit');
    Route::put('/posts/{id}', [UserPostController::class, 'update'])->whereNumber('id')->name('posts.update');
    Route::delete('/posts/{id}', [UserPostController::class, 'destroy'])->whereNumber('id')->name('posts.destroy');
    Route::put('/posts/{id}/evidence', [UserPostController::class, 'submitEvidence'])->whereNumber('id')->name('posts.evidence');
});

// ส่วนของ User แอดมิน: ต้องล็อกอินด้วยบัญชี role admin เท่านั้น
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::put('/admin/users/{uid}/ban', [AdminController::class, 'toggleBan'])->whereNumber('uid')->name('admin.users.ban');
    Route::delete('/admin/{iid}', [AdminController::class, 'destroy'])->whereNumber('iid')->name('admin.destroy');
    Route::put('/admin/{iid}/handover/confirm', [AdminController::class, 'confirmHandover'])->whereNumber('iid')->name('admin.handover.confirm');
    Route::put('/admin/{iid}/handover/reject', [AdminController::class, 'rejectHandover'])->whereNumber('iid')->name('admin.handover.reject');
});

require __DIR__.'/settings.php';
