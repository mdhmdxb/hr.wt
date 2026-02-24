<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\PayrollController;

Route::get('/', [PayrollController::class, 'index'])->name('index');
Route::get('/create', [PayrollController::class, 'create'])->name('create');
Route::post('/', [PayrollController::class, 'store'])->name('store');
Route::get('/payslip/{payslip}', [PayrollController::class, 'payslip'])->name('payslip');
Route::get('/payslip/{payslip}/qr', [PayrollController::class, 'payslipQr'])->name('payslip.qr');
Route::get('/payslip/{payslip}/edit', [PayrollController::class, 'editPayslip'])->name('payslip.edit');
Route::put('/payslip/{payslip}', [PayrollController::class, 'updatePayslip'])->name('payslip.update');
Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
Route::post('/{payroll}/finalize', [PayrollController::class, 'finalize'])->name('finalize');
