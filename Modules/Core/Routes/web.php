<?php
/**
 * Wise HRM – Developer: M H Morshed | Copyright © M H Morshed. Built with Laravel.
 */

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\AboutController;
use Modules\Core\Http\Controllers\BranchController;
use Modules\Core\Http\Controllers\CompanyLogoController;
use Modules\Core\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('role.owner')->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\OwnerController::class, 'index'])->name('index');
    Route::post('/modules', [\Modules\Core\Http\Controllers\OwnerController::class, 'updateModules'])->name('modules.update');
    Route::post('/options', [\Modules\Core\Http\Controllers\OwnerController::class, 'updateOptions'])->name('options.update');
});

Route::middleware(['auth', 'role.admin'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\UserAdminController::class, 'index'])->name('index');
    Route::post('/', [\Modules\Core\Http\Controllers\UserAdminController::class, 'update'])->name('update');
});
Route::middleware('auth')->post('/notifications/mark-read', [\Modules\Core\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
Route::middleware('auth')->get('/notifications/{id}/read', [\Modules\Core\Http\Controllers\NotificationController::class, 'readAndRedirect'])->name('notifications.read');
Route::middleware('auth')->get('/notifications', [\Modules\Core\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::post('/', [\Modules\Core\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::post('/signature', [\Modules\Core\Http\Controllers\ProfileController::class, 'updateSignature'])->name('signature.update');
});
Route::middleware('auth')->get('/account/password', [\Modules\Core\Http\Controllers\PasswordController::class, 'edit'])->name('password.edit');
Route::middleware('auth')->put('/account/password', [\Modules\Core\Http\Controllers\PasswordController::class, 'update'])->name('password.update');
Route::get('/dashboard/executive', [\Modules\Core\Http\Controllers\ExecutiveDashboardController::class, 'index'])->name('dashboard.executive')->middleware('role.admin');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/app/logo', [CompanyLogoController::class, 'show'])->name('app.logo');
Route::get('/app/favicon', [\Modules\Core\Http\Controllers\FaviconController::class, 'show'])->name('app.favicon');

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::get('/attendance', [\Modules\Core\Http\Controllers\ReportController::class, 'attendance'])->name('attendance');
    Route::get('/leave', [\Modules\Core\Http\Controllers\ReportController::class, 'leave'])->name('leave');
    Route::get('/payroll', [\Modules\Core\Http\Controllers\ReportController::class, 'payroll'])->name('payroll');
});

Route::middleware('role.admin')->prefix('companies')->name('company.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\CompanyController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\CompanyController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\CompanyController::class, 'store'])->name('store');
    Route::get('/{company}/edit', [\Modules\Core\Http\Controllers\CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [\Modules\Core\Http\Controllers\CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [\Modules\Core\Http\Controllers\CompanyController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.admin')->prefix('branches')->name('branch.')->group(function () {
    Route::get('/', [BranchController::class, 'index'])->name('index');
    Route::get('/create', [BranchController::class, 'create'])->name('create');
    Route::post('/', [BranchController::class, 'store'])->name('store');
    Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
    Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
    Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.admin')->prefix('sites')->name('site.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\SiteController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\SiteController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\SiteController::class, 'store'])->name('store');
    Route::get('/{site}/edit', [\Modules\Core\Http\Controllers\SiteController::class, 'edit'])->name('edit');
    Route::put('/{site}', [\Modules\Core\Http\Controllers\SiteController::class, 'update'])->name('update');
    Route::delete('/{site}', [\Modules\Core\Http\Controllers\SiteController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.hr')->prefix('departments')->name('department.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\DepartmentController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\DepartmentController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\DepartmentController::class, 'store'])->name('store');
    Route::get('/{department}/edit', [\Modules\Core\Http\Controllers\DepartmentController::class, 'edit'])->name('edit');
    Route::put('/{department}', [\Modules\Core\Http\Controllers\DepartmentController::class, 'update'])->name('update');
    Route::delete('/{department}', [\Modules\Core\Http\Controllers\DepartmentController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.hr')->prefix('designations')->name('designation.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\DesignationController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\DesignationController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\DesignationController::class, 'store'])->name('store');
    Route::get('/{designation}/edit', [\Modules\Core\Http\Controllers\DesignationController::class, 'edit'])->name('edit');
    Route::put('/{designation}', [\Modules\Core\Http\Controllers\DesignationController::class, 'update'])->name('update');
    Route::delete('/{designation}', [\Modules\Core\Http\Controllers\DesignationController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.hr')->prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\ProjectController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\ProjectController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\ProjectController::class, 'store'])->name('store');
    Route::get('/{project}/edit', [\Modules\Core\Http\Controllers\ProjectController::class, 'edit'])->name('edit');
    Route::put('/{project}', [\Modules\Core\Http\Controllers\ProjectController::class, 'update'])->name('update');
    Route::delete('/{project}', [\Modules\Core\Http\Controllers\ProjectController::class, 'destroy'])->name('destroy');
    Route::post('/{project}/employees', [\Modules\Core\Http\Controllers\ProjectController::class, 'attachEmployee'])->name('employees.attach');
    Route::delete('/{project}/employees/{employee}', [\Modules\Core\Http\Controllers\ProjectController::class, 'detachEmployee'])->name('employees.detach');
    Route::get('/{project}', [\Modules\Core\Http\Controllers\ProjectController::class, 'show'])->name('show');
});

Route::middleware('role.admin')->prefix('templates')->name('templates.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'store'])->name('store');
    Route::get('/{template}/preview', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'preview'])->name('preview');
    Route::get('/{template}/edit', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'edit'])->name('edit');
    Route::put('/{template}', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'update'])->name('update');
    Route::delete('/{template}', [\Modules\Core\Http\Controllers\DocumentTemplateController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role.admin'])->prefix('templates/email')->name('email-templates.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\EmailTemplateController::class, 'index'])->name('index');
    Route::post('/', [\Modules\Core\Http\Controllers\EmailTemplateController::class, 'update'])->name('update');
});

Route::middleware('auth')->prefix('my/documents')->name('my-documents.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'myIndex'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'myCreate'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'myStore'])->name('store');
    Route::get('/{document}/download', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'myDownload'])->name('download');
});

Route::middleware('role.hr')->prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'store'])->name('store');
    Route::get('/{document}/download', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'download'])->name('download');
    Route::get('/{document}', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'show'])->name('show');
    Route::delete('/{document}', [\Modules\Core\Http\Controllers\EmployeeDocumentController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.hr')->prefix('recruitment')->name('recruitment.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'index'])->name('index');
    Route::get('/openings/create', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'createOpening'])->name('openings.create');
    Route::post('/openings', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'storeOpening'])->name('openings.store');
    Route::get('/openings/{opening}/edit', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'editOpening'])->name('openings.edit');
    Route::put('/openings/{opening}', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'updateOpening'])->name('openings.update');
    Route::post('/openings/{opening}/candidates', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'storeCandidate'])->name('candidates.store');
    Route::put('/candidates/{candidate}/stage', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'updateCandidateStage'])->name('candidates.stage');
    Route::put('/candidates/{candidate}', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'updateCandidate'])->name('candidates.update');
    Route::get('/openings/{opening}', [\Modules\Core\Http\Controllers\RecruitmentController::class, 'showOpening'])->name('show');
});

Route::middleware('role.hr')->prefix('holidays')->name('holidays.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'store'])->name('store');
    Route::get('/{holiday}/edit', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'edit'])->name('edit');
    Route::put('/{holiday}', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'update'])->name('update');
    Route::delete('/{holiday}', [\Modules\Core\Http\Controllers\PublicHolidayController::class, 'destroy'])->name('destroy');
});

Route::middleware('role.hr')->prefix('assets')->name('assets.')->group(function () {
    Route::get('/', [\Modules\Core\Http\Controllers\AssetController::class, 'index'])->name('index');
    Route::get('/create', [\Modules\Core\Http\Controllers\AssetController::class, 'create'])->name('create');
    Route::post('/', [\Modules\Core\Http\Controllers\AssetController::class, 'store'])->name('store');
    Route::post('/{asset}/assign', [\Modules\Core\Http\Controllers\AssetController::class, 'assign'])->name('assign');
    Route::post('/{asset}/return', [\Modules\Core\Http\Controllers\AssetController::class, 'return'])->name('return');
    Route::get('/types', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'index'])->name('types.index');
    Route::get('/types/create', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'create'])->name('types.create');
    Route::post('/types', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'store'])->name('types.store');
    Route::get('/types/{type}/edit', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'edit'])->name('types.edit');
    Route::put('/types/{type}', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'update'])->name('types.update');
    Route::delete('/types/{type}', [\Modules\Core\Http\Controllers\AssetTypeController::class, 'destroy'])->name('types.destroy');
    Route::get('/{asset}/edit', [\Modules\Core\Http\Controllers\AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}', [\Modules\Core\Http\Controllers\AssetController::class, 'update'])->name('update');
    Route::get('/{asset}', [\Modules\Core\Http\Controllers\AssetController::class, 'show'])->name('show');
});
