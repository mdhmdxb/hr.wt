<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\EmployeeController;

Route::get('/', [EmployeeController::class, 'index'])->name('index');
Route::get('/create', [EmployeeController::class, 'create'])->name('create');
Route::post('/', [EmployeeController::class, 'store'])->name('store');
Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
