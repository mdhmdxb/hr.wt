<?php
/**
 * Wise HRM – Modular HR Management System
 * Developer: M H Morshed
 * Copyright © 2025 M H Morshed. Built with Laravel.
 */

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'name' => env('APP_NAME', 'Wise HRM'),
    'version' => env('APP_VERSION', '1.0.0'),
    'developer' => 'M H Morshed',
    'copyright' => 'Copyright © ' . date('Y') . ' M H Morshed. Built with Laravel.',
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        \Modules\Core\Providers\CoreServiceProvider::class,
        \Modules\Settings\Providers\SettingsServiceProvider::class,
        \Modules\Employee\Providers\EmployeeServiceProvider::class,
        \Modules\Attendance\Providers\AttendanceServiceProvider::class,
        \Modules\Leave\Providers\LeaveServiceProvider::class,
        \Modules\Payroll\Providers\PayrollServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

];
