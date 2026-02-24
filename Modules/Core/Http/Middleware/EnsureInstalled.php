<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $lockFile = storage_path('wise_hrm_installed.lock');

        if (! file_exists($lockFile)) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}
