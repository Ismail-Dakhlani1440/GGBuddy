<?php

namespace App\Policies;

use App\Models\Schedual;
use App\Models\User;

class SchedualPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEBuddy();
    }

    public function create(User $user): bool
    {
        return $user->isEBuddy();
    }

    public function delete(User $user, Schedual $schedual): bool
    {
        return $user->id === $schedual->e_buddy_id;
    }
}
