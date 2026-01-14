<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Lluvia;
use App\Models\Hacienda;
use App\Policies\UserPolicy;
use App\Policies\LluviaPolicy;
use App\Policies\HaciendaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Lluvia::class => LluviaPolicy::class,
        Hacienda::class => HaciendaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
public function boot(): void
{
    $this->registerPolicies();

    Gate::before(function ($user, $ability) {
        return $user->hasRole('admin') ? true : null;
    });
}
}
