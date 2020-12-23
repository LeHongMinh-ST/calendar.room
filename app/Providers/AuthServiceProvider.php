<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-admin', function ($user){
            return $user->role_id == 1;
        });

        Gate::define('view-auth', function ($user){
            return $user->role_id == 1 || $user->role_id ==0;
        });

        Gate::define('view-gv', function ($user){
            return $user->role_id ==0;
        });
    }
}
