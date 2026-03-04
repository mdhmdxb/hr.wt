<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\LeaveRequestController;

// Employee self-service leave routes (only their own requests)
Route::get('/', [LeaveRequestController::class, 'myIndex'])->name('index');
Route::get('/create', [LeaveRequestController::class, 'myCreate'])->name('create');
Route::post('/', [LeaveRequestController::class, 'myStore'])->name('store');
Route::get('/{leaveRequest}', [LeaveRequestController::class, 'myShow'])->name('show');
Route::post('/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');
Route::get('/{leaveRequest}/document', [LeaveRequestController::class, 'downloadDocument'])->name('document');

