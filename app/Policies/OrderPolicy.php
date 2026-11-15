<?php

namespace App\Policies;

use App\Models\Orders\Order;
use App\Models\Users\User;

class OrderPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Order $order): bool
    {
        return $order->customer_id === $user->user_id || $user->role?->role_name === 'admin';
    }

    public function create(User $user): bool { return true; }

    public function update(User $user, Order $order): bool
    {
        return $user->role?->role_name === 'admin';
    }

    public function cancel(User $user, Order $order): bool
    {
        return $order->customer_id === $user->user_id || $user->role?->role_name === 'admin';
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->role?->role_name === 'admin';
    }
}
