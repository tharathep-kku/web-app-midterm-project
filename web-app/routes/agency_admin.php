<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgencyItemController;
use App\Http\Controllers\AdminController;

// ส่วนของ User หน่วยงาน: โพสต์ของที่เก็บได้ พร้อมหลักฐานยืนยันและจุดสถานที่
Route::get('/agency', [AgencyItemController::class, 'index'])->name('agency.index');
Route::post('/agency/switch', [AgencyItemController::class, 'switchUser'])->name('agency.switch');
Route::get('/agency/create', [AgencyItemController::class, 'create'])->name('agency.create');
Route::post('/agency/store', [AgencyItemController::class, 'store'])->name('agency.store');
Route::get('/agency/{iid}', [AgencyItemController::class, 'show'])->whereNumber('iid')->name('agency.show');

// ส่วนของ User แอดมิน: อนุมัติโพสต์ ดูข้อมูลหลังบ้าน และสถิติต่างๆ
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/switch', [AdminController::class, 'switchUser'])->name('admin.switch');
Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
Route::put('/admin/{iid}/approve', [AdminController::class, 'approve'])->whereNumber('iid')->name('admin.approve');
Route::put('/admin/{iid}/reject', [AdminController::class, 'reject'])->whereNumber('iid')->name('admin.reject');
