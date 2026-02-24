<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('core', __DIR__ . '/../Resources/views');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        // Installer routes (only when not installed)
        Route::middleware(['web', 'not.installed'])
            ->prefix('install')
            ->name('install.')
            ->group(__DIR__ . '/../Routes/install.php');

        // Auth routes (login, logout) - when installed
        Route::middleware(['web', 'installed'])
            ->group(__DIR__ . '/../Routes/auth.php');

        // Public verification (no auth; token-secured)
        Route::middleware(['web', 'installed'])
            ->group(__DIR__ . '/../Routes/public.php');

        // Dashboard & main app routes - when installed + auth
        Route::middleware(['web', 'installed', 'auth', 'module.enabled'])
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
