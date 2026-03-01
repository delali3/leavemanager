<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestApproved extends Notification implements ShouldQueue
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
            ->subject('Leave Request Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your leave request has been **approved**.')
            ->line('**Leave Type:** ' . $request->leaveType->name)
            ->line('**From:** ' . $request->start_date->format('d M Y'))
            ->line('**To:** ' . $request->end_date->format('d M Y'))
            ->line('**Total Days:** ' . $request->total_days . ' working day(s)')
            ->when($request->manager_comment, fn ($mail) => $mail->line('**Comment:** ' . $request->manager_comment))
            ->action('View Request', route('leave-requests.show', $request))
            ->line('Enjoy your leave!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'leave_request_approved',
            'leave_request_id' => $this->leaveRequest->id,
            'leave_type'       => $this->leaveRequest->leaveType->name,
            'start_date'       => $this->leaveRequest->start_date->format('Y-m-d'),
            'end_date'         => $this->leaveRequest->end_date->format('Y-m-d'),
        ];
    }
}
