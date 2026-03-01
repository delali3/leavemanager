<?php

namespace App\Listeners;

use App\Events\LeaveRequestApproved;
use App\Notifications\LeaveRequestApproved as LeaveRequestApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeaveApproval implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(LeaveRequestApproved $event): void
    {
        $event->leaveRequest->user->notify(
            new LeaveRequestApprovedNotification($event->leaveRequest)
        );
    }
}
