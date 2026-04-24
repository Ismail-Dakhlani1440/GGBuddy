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
        $now     = now();
        $today   = $now->format('l'); // e.g. "Monday"
        $current = $now->format('H:i:s');

        return $this->day_of_week === $today
            && $current >= $this->start_time
            && $current <= $this->end_time;
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }
}