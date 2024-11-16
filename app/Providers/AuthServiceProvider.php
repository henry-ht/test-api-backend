<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(Request $request): void
    {

        // Gate::define('', function($user){
        //     return true;
        // });

        // Passport::tokensExpireIn(now()->addMinutes(1));
        // Passport::refreshTokensExpireIn(now()->addDays(30));

        /* define a SuperAdmin user role */
        Gate::define('is_super_admin', function($user) {
            return str_contains($user->role->name, 'super_admin');
        });

        /* define a AppAdmin user role */
        Gate::define('is_admin', function($user) {
            return str_contains($user->role->name, 'app_admin');
        });

        Gate::define('isAccessible', function($user) use ($request) {
            // $request->route()->getName()
            return true;
        });
    }
}
