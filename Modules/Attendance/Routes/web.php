<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::get('/', [AttendanceController::class, 'index'])->name('index');
Route::get('/batch', [AttendanceController::class, 'batch'])->name('batch');
Route::post('/batch', [AttendanceController::class, 'storeBatch'])->name('batch.store');
Route::get('/create', [AttendanceController::class, 'create'])->name('create');
Route::post('/', [AttendanceController::class, 'store'])->name('store');
Route::get('/{attendance}/attachment', [AttendanceController::class, 'downloadAttachment'])->name('attachment.download');
Route::post('/{attendance}/unlock', [AttendanceController::class, 'unlock'])->name('unlock');
Route::post('/submission/allow-edit', [AttendanceController::class, 'allowEditSubmission'])->name('submission.allow-edit');
Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
