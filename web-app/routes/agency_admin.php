<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgencyItemController;
use App\Http\Controllers\AdminController;

// ส่วนของ User หน่วยงาน: ต้องล็อกอินด้วยบัญชี role agency เท่านั้น
Route::middleware(['auth', 'agency'])->group(function () {
    Route::get('/agency', [AgencyItemController::class, 'index'])->name('agency.index');
    Route::get('/agency/create', [AgencyItemController::class, 'create'])->name('agency.create');
    Route::post('/agency/store', [AgencyItemController::class, 'store'])->name('agency.store');
    Route::get('/agency/{iid}', [AgencyItemController::class, 'show'])->whereNumber('iid')->name('agency.show');
});

// ส่วนของ User แอดมิน: ต้องล็อกอินด้วยบัญชี role admin เท่านั้น
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::put('/admin/{iid}/approve', [AdminController::class, 'approve'])->whereNumber('iid')->name('admin.approve');
    Route::put('/admin/{iid}/reject', [AdminController::class, 'reject'])->whereNumber('iid')->name('admin.reject');
});
