<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        // Define Gate for menu access
        // Usage: Gate::allows('access-menu', 'MJ-01') or @can('access-menu', 'MJ-01')
        Gate::define('access-menu', function ($user, $menuCode) {
            // Get menu by code
            $menu = Menu::where('code', $menuCode)->first();
            
            if (!$menu) {
                return false;
            }

            // Check if user's role has permission to access this menu
            $permission = Permission::where('role_id', $user->role_id)
                ->where('menu_id', $menu->id)
                ->where('status', 1) // 1 = full access
                ->first();

            return $permission !== null;
        });

        // Define Gate for checking any menu access by URL
        Gate::define('access-url', function ($user, $url) {
            // Get menu by URL
            $menu = Menu::where('url', $url)->first();
            
            if (!$menu) {
                return false;
            }

            // Check if user's role has permission to access this menu
            $permission = Permission::where('role_id', $user->role_id)
                ->where('menu_id', $menu->id)
                ->where('status', 1) // 1 = full access
                ->first();

            return $permission !== null;
        });
    }
}

