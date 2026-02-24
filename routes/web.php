<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Wise HRM)
|--------------------------------------------------------------------------
| Installer is loaded by CoreServiceProvider when not installed.
| Authenticated routes use 'installed' + 'auth' middleware.
*/

Route::get('/', function () {
    if (file_exists(storage_path('wise_hrm_installed.lock'))) {
        return redirect()->route('login');
    }
    return redirect()->route('install.welcome');
});

// Module routes are registered in each module's service provider.
