<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::get('/', [SettingsController::class, 'general'])->name('general');
Route::post('/general', [SettingsController::class, 'storeGeneral'])->name('general.store');
Route::post('/appearance', [SettingsController::class, 'storeAppearance'])->name('appearance.store');
Route::post('/mail', [SettingsController::class, 'storeMail'])->name('mail.store');
