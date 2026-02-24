<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Auth\WebOrOwnerUserProvider;
use Modules\Core\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('web_or_owner', function ($app, array $config) {
            return new WebOrOwnerUserProvider(
                $app['hash'],
                $config['model'] ?? User::class
            );
        });
    }
}
