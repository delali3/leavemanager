<?php

namespace App\Providers;

use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Listeners\HandleLeaveApproval;
use App\Listeners\HandleLeaveRejection;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Policies\LeaveRequestPolicy;
use App\Policies\LeaveTypePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Register Policies
        Gate::policy(LeaveType::class, LeaveTypePolicy::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Super admin gate — admin role bypasses all policy checks
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Register Events → Listeners
        Event::listen(Registered::class, SendEmailVerificationNotification::class);
        Event::listen(LeaveRequestApproved::class, HandleLeaveApproval::class);
        Event::listen(LeaveRequestRejected::class, HandleLeaveRejection::class);
    }
}
