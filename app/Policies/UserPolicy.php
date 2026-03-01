<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr']);
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasAnyRole(['admin', 'hr', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'hr']);
    }

    public function update(User $user, User $model): bool
    {
        // Admin can update anyone; HR can update non-admins; users can update themselves
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('hr')) {
            return !$model->hasRole('admin');
        }
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $user->id !== $model->id;
    }
}
