<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
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