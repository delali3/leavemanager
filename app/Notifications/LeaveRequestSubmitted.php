<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->leaveRequest;

        return (new MailMessage)
            ->subject('New Leave Request Submitted - ' . $request->user->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($request->user->name . ' has submitted a leave request.')
            ->line('**Leave Type:** ' . $request->leaveType->name)
            ->line('**From:** ' . $request->start_date->format('d M Y'))
            ->line('**To:** ' . $request->end_date->format('d M Y'))
            ->line('**Total Days:** ' . $request->total_days . ' working day(s)')
            ->line('**Reason:** ' . $request->reason)
            ->action('Review Request', route('leave-requests.show', $request))
            ->line('Please review and take action on this request.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'leave_request_submitted',
            'leave_request_id' => $this->leaveRequest->id,
            'user_name'        => $this->leaveRequest->user->name,
            'leave_type'       => $this->leaveRequest->leaveType->name,
            'start_date'       => $this->leaveRequest->start_date->format('Y-m-d'),
            'end_date'         => $this->leaveRequest->end_date->format('Y-m-d'),
            'total_days'       => $this->leaveRequest->total_days,
        ];
    }
}
