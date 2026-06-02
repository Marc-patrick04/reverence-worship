<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is disabled.');
        }

        // Super Admin has all access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // If no specific permission required, just allow
        if (!$permission) {
            return $next($request);
        }

        // Parse page and feature from permission string (format: page.feature)
        $parts = explode('.', $permission);
        if (count($parts) == 2) {
            $pageName = $parts[0];
            $featureName = $parts[1];
            
            if (!$user->canAccess($pageName, $featureName)) {
                abort(403, 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}