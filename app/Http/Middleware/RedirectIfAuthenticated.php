<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if (Auth::user()->isSuperAdmin()) {
                    return redirect()->route('super-admin.dashboard');
                }
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}