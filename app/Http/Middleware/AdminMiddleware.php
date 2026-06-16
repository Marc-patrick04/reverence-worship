<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Admin only.');
        }
        
        return $next($request);
    }
}