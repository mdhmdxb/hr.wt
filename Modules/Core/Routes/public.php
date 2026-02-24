<?php
/**
 * Public routes (no login required). Token-secured verification only.
 */
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'installed'])->group(function () {
    Route::get('/verify/payslip/{payslip}', [\Modules\Payroll\Http\Controllers\PayrollVerificationController::class, 'show'])
        ->name('payroll.verify');
    Route::get('/verify/leave/{leaveRequest}', [\Modules\Leave\Http\Controllers\LeaveVerificationController::class, 'show'])
        ->name('leave.verify');
});
