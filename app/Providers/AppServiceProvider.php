<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MySQL/InnoDB key length limit (767/1000 bytes); utf8mb4 = 4 bytes/char.
        // 191 chars = 764 bytes, safe for unique indexes on string columns.
        Schema::defaultStringLength(191);
    }
}
