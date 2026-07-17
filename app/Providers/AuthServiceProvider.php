<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-certificates', function ($user): bool {
            if ($user instanceof Admin) {
                return $user->id === 1
                    || in_array('certificates.manage.templates.index', $user->menu_permissions ?? [], true);
            }

            return false;
        });
    }
}
