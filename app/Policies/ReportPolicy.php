<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;

class ReportPolicy
{
    /**
     * Can the user report the target?
     */
    public function create(User $user, User $target): bool
    {
        // Users cannot report themselves
        if ($user->id === $target->id) {
            return false;
        }

        // Check if there is already a pending report from this user for this target
        $exists = \App\Models\Report::where('reporter_id', $user->id)
            ->where('target_id', $target->id)
            ->where('status', 'pending')
            ->exists();

        return !$exists;
    }

    /**
     * Can the user see reports for this target?
     * (Owners cannot see their own reports in this context)
     */
    public function viewReports(User $user, User $target): bool
    {
        // Admin can see all, but for the profile view logic:
        // We only show the button/count to OTHERS
        return $user->id !== $target->id;
    }
}
