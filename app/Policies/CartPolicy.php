<?php

namespace App\Policies;

use App\Models\Carts\Cart;
use App\Models\Users\User;

class CartPolicy
{
    public function view(User $user, Cart $cart): bool
    {
        return $cart->user_id === $user->user_id;
    }

    public function update(User $user, Cart $cart): bool
    {
        return $cart->user_id === $user->user_id;
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $cart->user_id === $user->user_id;
    }
}
