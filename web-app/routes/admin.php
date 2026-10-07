<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// ส่วนของ User แอดมิน: ต้องล็อกอินด้วยบัญชี role admin เท่านั้น
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::put('/admin/{iid}/approve', [AdminController::class, 'approve'])->whereNumber('iid')->name('admin.approve');
    Route::put('/admin/{iid}/reject', [AdminController::class, 'reject'])->whereNumber('iid')->name('admin.reject');
});
