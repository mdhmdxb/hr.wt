<?php

namespace Modules\Settings\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('settings', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'installed', 'auth', 'role.admin'])
            ->prefix('settings')
            ->name('settings.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
