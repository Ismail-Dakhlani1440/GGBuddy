<?php

namespace App\Policies;

use App\Models\PlayerGameProfile;
use App\Models\User;

class PlayerGameProfilePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, PlayerGameProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }
}
