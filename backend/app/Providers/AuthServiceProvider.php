<?php

namespace App\Providers;

use App\Models\Student;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Student::class => \App\Policies\StudentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // You can also define Gates here if needed
        // Gate::define('something', fn(User $user) => ...);
    }
}
