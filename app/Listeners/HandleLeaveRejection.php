<?php

namespace App\Listeners;

use App\Events\LeaveRequestRejected;
use App\Notifications\LeaveRequestRejected as LeaveRequestRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeaveRejection implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(LeaveRequestRejected $event): void
    {
        $event->leaveRequest->user->notify(
            new LeaveRequestRejectedNotification($event->leaveRequest)
        );
    }
}
