<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view the orders page
    }

    public function viewIncoming(User $user): bool
    {
        return $user->isEBuddy();
    }

    public function viewOutgoing(User $user): bool
    {
        return true; // Everyone can have outgoing orders (as a player)
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->e_buddy_id || $user->id === $order->player_id;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->e_buddy_id;
    }
}
