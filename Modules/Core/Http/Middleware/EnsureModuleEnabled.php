<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /** Route name prefix or full name -> module key */
    protected static array $routeModuleMap = [
        'attendance.' => 'attendance',
        'leave.' => 'leave',
        'payroll.' => 'payroll',
        'assets.' => 'assets',
        'recruitment.' => 'recruitment',
        'documents.' => 'documents',
        'reports.' => 'reports',
        'templates.' => 'templates',
        'designation.' => 'designations',
        'company.' => 'companies',
        'branch.' => 'branches',
        'site.' => 'sites',
        'projects.' => 'projects',
        'employee.' => 'employees',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (auth()->user()->isOwner()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (! $routeName) {
            return $next($request);
        }

        $module = null;
        foreach (self::$routeModuleMap as $prefix => $mod) {
            if (str_starts_with($routeName, $prefix) || $routeName === $prefix) {
                $module = $mod;
                break;
            }
        }

        if ($module !== null && ! Setting::isModuleEnabled($module)) {
            abort(403, 'This module is disabled.');
        }

        return $next($request);
    }
}
