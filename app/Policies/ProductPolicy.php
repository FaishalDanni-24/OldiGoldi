<?php

namespace App\Policies;

use App\Models\Products\Product;
use App\Models\Users\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Product $product): bool { return true; }

    public function create(User $user): bool
    {
        return in_array($user->role?->role_name, ['admin', 'seller'], true);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role?->role_name === 'admin' || $product->user_id === $user->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role?->role_name === 'admin' || $product->user_id === $user->user_id;
    }
}
