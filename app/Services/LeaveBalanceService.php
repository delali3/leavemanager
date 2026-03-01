<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;

class LeaveBalanceService
{
    /**
     * Initialize leave balances for a new user based on all active leave types.
     */
    public function initializeForUser(User $user, int $year = null): void
    {
        $year = $year ?? now()->year;

        LeaveType::all()->each(function (LeaveType $leaveType) use ($user, $year) {
            LeaveBalance::firstOrCreate(
                [
                    'user_id'       => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year'          => $year,
                ],
                [
                    'total_days'     => $leaveType->max_days,
                    'used_days'      => 0,
                    'remaining_days' => $leaveType->max_days,
                ]
            );
        });
    }

    /**
     * Deduct days from a user's leave balance after approval.
     */
    public function deduct(User $user, int $leaveTypeId, int $days, int $year = null): void
    {
        $year = $year ?? now()->year;

        $balance = LeaveBalance::firstOrCreate(
            [
                'user_id'       => $user->id,
                'leave_type_id' => $leaveTypeId,
                'year'          => $year,
            ],
            [
                'total_days'     => 0,
                'used_days'      => 0,
                'remaining_days' => 0,
            ]
        );

        $balance->used_days      = $balance->used_days + $days;
        $balance->remaining_days = max(0, $balance->total_days - $balance->used_days);
        $balance->save();
    }

    /**
     * Restore days to a user's leave balance (e.g., after rejection or cancellation).
     */
    public function restore(User $user, int $leaveTypeId, int $days, int $year = null): void
    {
        $year    = $year ?? now()->year;
        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();

        if ($balance) {
            $balance->used_days      = max(0, $balance->used_days - $days);
            $balance->remaining_days = max(0, $balance->total_days - $balance->used_days);
            $balance->save();
        }
    }

    /**
     * Get all balances for a user for the current year.
     */
    public function getForUser(User $user, int $year = null): \Illuminate\Database\Eloquent\Collection
    {
        return LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $year ?? now()->year)
            ->get();
    }

    /**
     * Check if user has enough balance for a leave type.
     */
    public function hasEnoughBalance(User $user, int $leaveTypeId, int $days, int $year = null): bool
    {
        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year ?? now()->year)
            ->first();

        if (!$balance) {
            return false;
        }

        return $balance->remaining_days >= $days;
    }

    /**
     * Carry forward remaining days for a user to the next year.
     */
    public function carryForward(User $user, int $fromYear, int $toYear): void
    {
        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $fromYear)
            ->get();

        foreach ($balances as $balance) {
            if ($balance->leaveType && $balance->leaveType->carry_forward && $balance->remaining_days > 0) {
                $existing = LeaveBalance::where('user_id', $user->id)
                    ->where('leave_type_id', $balance->leave_type_id)
                    ->where('year', $toYear)
                    ->first();

                if ($existing) {
                    $existing->total_days     += $balance->remaining_days;
                    $existing->remaining_days  = $existing->total_days - $existing->used_days;
                    $existing->save();
                }
            }
        }
    }
}
