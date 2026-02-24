<?php

namespace Modules\Leave\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LeaveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('leave', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'installed', 'auth', 'role.hr'])
            ->prefix('leave')
            ->name('leave.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
