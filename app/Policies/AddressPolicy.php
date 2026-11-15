<?php

namespace App\Policies;

use App\Models\Users\Address;
use App\Models\Users\User;

class AddressPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Address $address): bool
    {
        return $address->user_id === $user->user_id || $user->role?->role_name === 'admin';
    }

    public function create(User $user): bool { return true; }

    public function update(User $user, Address $address): bool
    {
        return $address->user_id === $user->user_id || $user->role?->role_name === 'admin';
    }

    public function delete(User $user, Address $address): bool
    {
        return $address->user_id === $user->user_id || $user->role?->role_name === 'admin';
    }
}
