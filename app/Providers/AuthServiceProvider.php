<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Policies\EmployeePolicy;
use App\Policies\AttendancePolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        Attendance::class => AttendancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register policies
        $this->registerPolicies();

        // Define admin gate for authorization
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
