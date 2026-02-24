<?php

namespace Modules\Payroll\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class PayrollServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('payroll', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'installed', 'auth', 'role.accounts'])
            ->prefix('payroll')
            ->name('payroll.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
