<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::get('/', [SettingsController::class, 'general'])->name('general');
Route::post('/general', [SettingsController::class, 'storeGeneral'])->name('general.store');
Route::post('/appearance', [SettingsController::class, 'storeAppearance'])->name('appearance.store');
Route::post('/mail', [SettingsController::class, 'storeMail'])->name('mail.store');
Route::post('/mail/test', [SettingsController::class, 'sendTestMail'])->name('mail.test');
Route::post('/imap', [SettingsController::class, 'storeImap'])->name('imap.store');
Route::post('/payslip-display', [SettingsController::class, 'storePayslipDisplay'])->name('payslip.display.store');
Route::post('/overtime', [SettingsController::class, 'storeOvertimeSettings'])->name('overtime.store');
Route::post('/signature', [SettingsController::class, 'storeSignature'])->name('signature.store');
Route::post('/company-stamp', [SettingsController::class, 'storeCompanyStamp'])->name('company-stamp.store');
Route::post('/document-display', [SettingsController::class, 'storeDocumentDisplay'])->name('document-display.store');
Route::post('/working-schedule-overrides', [SettingsController::class, 'storeWorkingScheduleOverride'])->name('working-schedule-overrides.store');
Route::delete('/working-schedule-overrides/{override}', [SettingsController::class, 'destroyWorkingScheduleOverride'])->name('working-schedule-overrides.destroy');
Route::post('/dashboard-cards', [SettingsController::class, 'storeDashboardCards'])->name('dashboard-cards.store');
