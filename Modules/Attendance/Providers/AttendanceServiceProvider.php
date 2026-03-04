<?php

namespace Modules\Attendance\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('attendance', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        // Employee self: check-in/out + my attendance batch (no HR required)
        Route::middleware(['web', 'installed', 'auth'])
            ->prefix('attendance')
            ->name('attendance.')
            ->group(function () {
                Route::post('/self/check-in', [\Modules\Attendance\Http\Controllers\AttendanceController::class, 'selfCheckIn'])->name('self.check-in');
                Route::post('/self/check-out', [\Modules\Attendance\Http\Controllers\AttendanceController::class, 'selfCheckOut'])->name('self.check-out');
                Route::get('/my-attendance', [\Modules\Attendance\Http\Controllers\AttendanceController::class, 'myAttendance'])->name('my');
                Route::post('/my-attendance', [\Modules\Attendance\Http\Controllers\AttendanceController::class, 'storeMyBatch'])->name('my.store');
                Route::post('/my-attendance/submit', [\Modules\Attendance\Http\Controllers\AttendanceController::class, 'submitMyMonth'])->name('my.submit');
            });

        // All other attendance routes: Admin or HR only
        Route::middleware(['web', 'installed', 'auth', 'role.hr'])
            ->prefix('attendance')
            ->name('attendance.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
