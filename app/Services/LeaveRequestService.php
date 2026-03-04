<?php

namespace App\Services;

use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class LeaveRequestService
{
    public function __construct(
        private readonly LeaveBalanceService $balanceService
    ) {}

    /**
     * Create a new leave request with all validations.
     *
     * @throws \RuntimeException
     */
    public function create(User $user, array $data, ?UploadedFile $attachment = null): LeaveRequest
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate   = Carbon::parse($data['end_date']);
        $totalDays = LeaveRequest::calculateWorkingDays($startDate, $endDate);

        $leaveType = LeaveType::findOrFail($data['leave_type_id']);

        // Check hr_only restriction
        if ($leaveType->hr_only && !$user->hasAnyRole(['admin', 'hr'])) {
            throw new \RuntimeException('Unpaid leave can only be arranged by HR. Please contact your HR department.');
        }

        // Check balance
        if (!$this->balanceService->hasEnoughBalance($user, $leaveType->id, $totalDays)) {
            throw new \RuntimeException('Insufficient leave balance for this request.');
        }

        // Check for overlapping leaves
        if ($this->hasOverlap($user, $startDate, $endDate)) {
            throw new \RuntimeException('You already have an approved or pending leave request for this period.');
        }

        return DB::transaction(function () use ($user, $data, $startDate, $endDate, $totalDays, $leaveType, $attachment) {
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $attachment->store('leave-attachments', 'public');
            }

            $request = LeaveRequest::create([
                'user_id'       => $user->id,
                'leave_type_id' => $leaveType->id,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'total_days'    => $totalDays,
                'reason'        => $data['reason'],
                'attachment'    => $attachmentPath,
                'status'        => 'pending',
            ]);

            // Notify managers
            $managers = User::role('manager')->get();
            foreach ($managers as $manager) {
                $manager->notify(new \App\Notifications\LeaveRequestSubmitted($request));
            }
            // Also notify HR and Admin
            $admins = User::role(['admin', 'hr'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\LeaveRequestSubmitted($request));
            }

            return $request;
        });
    }

    /**
     * Approve a leave request.
     *
     * @throws \RuntimeException
     */
    public function approve(LeaveRequest $request, User $approver, string $comment = null): LeaveRequest
    {
        if (!$request->isPending()) {
            throw new \RuntimeException('Only pending leave requests can be approved.');
        }

        DB::transaction(function () use ($request, $approver, $comment) {
            $request->update([
                'status'          => 'approved',
                'manager_comment' => $comment,
                'approved_by'     => $approver->id,
                'approved_at'     => now(),
            ]);

            // Deduct balance (use year of the start_date)
            $this->balanceService->deduct(
                $request->user,
                $request->leave_type_id,
                $request->total_days,
                $request->start_date->year
            );

            event(new LeaveRequestApproved($request));
        });

        return $request->refresh();
    }

    /**
     * Reject a leave request.
     *
     * @throws \RuntimeException
     */
    public function reject(LeaveRequest $request, User $rejector, string $comment = null): LeaveRequest
    {
        if (!$request->isPending()) {
            throw new \RuntimeException('Only pending leave requests can be rejected.');
        }

        DB::transaction(function () use ($request, $rejector, $comment) {
            $request->update([
                'status'          => 'rejected',
                'manager_comment' => $comment,
                'approved_by'     => $rejector->id,
                'approved_at'     => now(),
            ]);

            event(new LeaveRequestRejected($request));
        });

        return $request->refresh();
    }

    /**
     * Check if a user has overlapping leave requests.
     */
    public function hasOverlap(User $user, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): bool
    {
        $query = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get monthly leave statistics for the dashboard.
     */
    public function getMonthlyStats(int $year = null): array
    {
        $year = $year ?? now()->year;

        return LeaveRequest::selectRaw('MONTH(start_date) as month, COUNT(*) as total')
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->month => $item->total])
            ->toArray();
    }
}
