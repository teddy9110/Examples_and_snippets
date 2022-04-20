<?php

namespace Rhf\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserRole;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Rhf\Model' => 'Rhf\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerPostPolicies();

        Auth::viaRequest('admin', function ($request) {
            $adminGodRoles = UserRole::whereIn('slug', ['admin', 'higher-admin', 'god'])->pluck('id')->toArray();
            return User::where('email', '=', $request->email)->whereIn('role_id', $adminGodRoles)->first();
        });
    }

    public function registerPostPolicies()
    {
        $policies = [
            'view:facebook-content',
            'add:facebook-content',
            'delete:facebook-content',
            'list:content',
            'add:content',
            'view:content',
            'update:content',
            'delete:content',
            'list:categories',
            'add:category',
            'view:category',
            'update:category',
            'delete:category',
            'list:users',
            'add:user',
            'view:user',
            'update:user',
            'delete:user',
            'purge:user',
            'restore:user',
            'list:recipes',
            'view:recipe',
            'add:recipe',
            'update:recipe',
            'delete:recipe',
            'unlink-mfp:user',
            'update:user-password',
            'update:user-permission',
            'view:user-progress-pictures',
            'delete:user-progress-picture',
            'view:services',
            'update:service',
            'view:promoted-products',
            'add:promoted-product',
            'update:promoted-product',
            'delete:promoted-product',
            'view:dashboard',
            'admin:exercises',
            'admin:workouts',
            'view:exercise-preferences',
            'view:notification',
            'update:notification',
            'send:notification',
            'delete:notification',
            'view:topic',
            'update:topic',
            'delete:topic',
            'update:tags',
            'list:direct-debit-signups',
            'create:direct-debit-signups',
            'view:transformations',
            'delete:transformations',
            'manage:subscriptions',
            'read:features',
            'manage:features',
            'manage:direct-debits',
            'read:features',
            'manage:features',
            'view:videos',
            'manage:videos',
            'view:competitions',
            'manage:competitions',
        ];

        foreach ($policies as $policy) {
            // Only allow password updates for roles lower than the auth user
            if ($policy == 'update:user-password') {
                Gate::define($policy, function ($user, $editUser) use ($policy) {
                    // Firstly check if they have the policy to update passwords
                    $hasPolicy = $user->hasAccess([$policy]);

                    if ($hasPolicy && $user->role->slug == 'god') {
                        return true;
                    }

                    // If they do have edit password functionality, only allow them if the edit user role is customer
                    if ($hasPolicy && $editUser->role->slug == 'customer') {
                        return true;
                    }

                    // If the user doesn't have the policy return false
                    return false;
                });
            } else {
                Gate::define($policy, function ($user) use ($policy) {
                    return $user->hasAccess([$policy]);
                });
            }
        }
    }
}
