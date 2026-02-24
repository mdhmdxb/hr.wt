<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\InstallController;

Route::get('/', [InstallController::class, 'welcome'])->name('welcome');
Route::get('/database', [InstallController::class, 'database'])->name('database');
Route::post('/database', [InstallController::class, 'storeDatabase'])->name('database.store');
Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
Route::post('/admin', [InstallController::class, 'storeAdmin'])->name('admin.store');
Route::get('/company', [InstallController::class, 'company'])->name('company');
Route::post('/company', [InstallController::class, 'storeCompany'])->name('company.store');
Route::get('/finalize', [InstallController::class, 'finalize'])->name('finalize');
Route::post('/finalize', [InstallController::class, 'complete'])->name('complete');
