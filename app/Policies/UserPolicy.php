<?php

namespace App\Policies;

use App\Models\Users\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->role_name === 'admin';
    }

    public function view(User $user, User $target): bool
    {
        return $user->user_id === $target->user_id || $user->role?->role_name === 'admin';
    }

    public function update(User $user, User $target): bool
    {
        return $user->user_id === $target->user_id || $user->role?->role_name === 'admin';
    }

    public function delete(User $user, User $target): bool
    {
        return $user->role?->role_name === 'admin' && $user->user_id !== $target->user_id;
    }
}
