<?php

namespace VanguardLTE\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use VanguardLTE\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'VanguardLTE\Model' => 'VanguardLTE\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        \Blade::directive('role', function ($expression) {
            return "<?php if (\\Auth::user()->hasRole({$expression})) : ?>";
        });

        \Blade::directive('endrole', function ($expression) {
            return "<?php endif; ?>";
        });

        \Blade::directive('permission', function ($expression) {
            return "<?php if (\\Auth::user()->hasPermission({$expression})) : ?>";
        });
        
        \Blade::directive('endpermission', function ($expression) {
            return "<?php endif; ?>";
        });

        \Gate::define('manage-session', function (User $user, $session) {
            if ($user->hasPermission('users.manage')) {
                return true;
            }

            return (int) $user->id === (int) $session->user_id;
        });
    }
}
