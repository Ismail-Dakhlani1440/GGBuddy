<?php

namespace App\Policies;

use App\Models\Unavailability;
use App\Models\User;

class UnavailabilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEBuddy();
    }

    public function create(User $user): bool
    {
        return $user->isEBuddy();
    }

    public function delete(User $user, Unavailability $unavailability): bool
    {
        return $user->id === $unavailability->e_buddy_id;
    }
}
