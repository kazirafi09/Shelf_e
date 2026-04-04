<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            // Allow all /admin/* routes and logout through
            if (!$request->is('admin/*') && !$request->is('admin') && !$request->is('logout')) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
