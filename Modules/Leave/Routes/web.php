<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\LeaveRequestController;
use Modules\Leave\Http\Controllers\LeaveTypeController;

Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
Route::get('/calendar', [LeaveRequestController::class, 'calendar'])->name('calendar');
Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
Route::get('/types', [LeaveTypeController::class, 'index'])->name('types.index');
Route::get('/types/create', [LeaveTypeController::class, 'create'])->name('types.create');
Route::post('/types', [LeaveTypeController::class, 'store'])->name('types.store');
Route::get('/types/{type}/edit', [LeaveTypeController::class, 'edit'])->name('types.edit');
Route::put('/types/{type}', [LeaveTypeController::class, 'update'])->name('types.update');
Route::delete('/types/{type}', [LeaveTypeController::class, 'destroy'])->name('types.destroy');
Route::get('/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('show');
Route::get('/{leaveRequest}/letter', [LeaveRequestController::class, 'downloadLetter'])->name('letter');
Route::get('/{leaveRequest}/qr', [LeaveRequestController::class, 'leaveQr'])->name('qr');
Route::post('/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
Route::post('/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
