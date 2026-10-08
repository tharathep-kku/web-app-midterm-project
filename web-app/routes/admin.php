<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// ส่วนของ User แอดมิน: ต้องล็อกอินด้วยบัญชี role admin เท่านั้น
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::delete('/admin/{iid}', [AdminController::class, 'destroy'])->whereNumber('iid')->name('admin.destroy');
    Route::put('/admin/{iid}/handover/confirm', [AdminController::class, 'confirmHandover'])->whereNumber('iid')->name('admin.handover.confirm');
    Route::put('/admin/{iid}/handover/reject', [AdminController::class, 'rejectHandover'])->whereNumber('iid')->name('admin.handover.reject');
});
