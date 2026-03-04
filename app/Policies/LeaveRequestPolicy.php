<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Owner, manager, HR, or admin can view
        return $user->id === $leaveRequest->user_id
            || $user->hasAnyRole(['admin', 'hr', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        // Only owner can edit, and only when pending
        return $user->id === $leaveRequest->user_id
            && $leaveRequest->isPending();
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        // Owner can delete pending; admin/HR can delete any
        return ($user->id === $leaveRequest->user_id && $leaveRequest->isPending())
            || $user->hasAnyRole(['admin', 'hr']);
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasAnyRole(['admin', 'hr'])
            && $leaveRequest->isPending()
            && $user->id !== $leaveRequest->user_id;
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasAnyRole(['admin', 'hr'])
            && $leaveRequest->isPending()
            && $user->id !== $leaveRequest->user_id;
    }
}
