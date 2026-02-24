<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdmin() && ! $user->isManager())) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
