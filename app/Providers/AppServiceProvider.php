<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register permission directives
        Blade::if('canView', function ($pageName) {
            return auth()->check() && auth()->user()->canAccess($pageName, 'view');
        });
        
        Blade::if('canCreate', function ($pageName) {
            return auth()->check() && auth()->user()->canAccess($pageName, 'create');
        });
        
        Blade::if('canEdit', function ($pageName) {
            return auth()->check() && auth()->user()->canAccess($pageName, 'edit');
        });
        
        Blade::if('canDelete', function ($pageName) {
            return auth()->check() && auth()->user()->canAccess($pageName, 'delete');
        });
    }
}
