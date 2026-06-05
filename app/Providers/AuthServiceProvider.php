<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
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

public function boot()
{
    $this->registerPolicies();

    // هذا السطر يجعل السوبر أدمن يتخطى كل فحوصات الصلاحيات ويفتح له كل شيء
    Gate::before(function ($user, $ability) {
        return $user->hasRole('super-admin', 'admin') ? true : null;
    });
}
}
