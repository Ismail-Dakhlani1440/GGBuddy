<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedual extends Model
{
    protected $fillable = [
        'e_buddy_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * True if today matches this slot's day and current time is within the window.
     */
    public function isActiveNow(): bool
    {
        $now = now();
        
        // Match day of week (e.g. "Tuesday")
        if (trim($this->day_of_week) !== $now->format('l')) {
            return false;
        }

        // Parse start and end times for today
        $start = \Carbon\Carbon::createFromTimeString($this->start_time, $now->timezone);
        $end   = \Carbon\Carbon::createFromTimeString($this->end_time, $now->timezone);

        return $now->between($start, $end);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }
}