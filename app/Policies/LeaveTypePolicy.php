<?php

namespace App\Policies;

use App\Models\LeaveType;
use App\Models\User;

class LeaveTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr', 'manager', 'employee']);
    }

    public function view(User $user, LeaveType $leaveType): bool
    {
        return $user->hasAnyRole(['admin', 'hr', 'manager', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr']);
    }

    public function update(User $user, LeaveType $leaveType): bool
    {
        return $user->hasAnyRole(['admin', 'hr']);
    }

    public function delete(User $user, LeaveType $leaveType): bool
    {
        return $user->hasAnyRole(['admin', 'hr']);
    }

    public function restore(User $user, LeaveType $leaveType): bool
    {
        return $user->hasRole('admin');
    }
}
