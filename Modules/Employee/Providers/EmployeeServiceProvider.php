<?php

namespace Modules\Employee\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('employee', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'installed', 'auth', 'role.hr'])
            ->prefix('employees')
            ->name('employee.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
