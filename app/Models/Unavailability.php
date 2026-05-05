<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unavailability extends Model
{
    protected $fillable = [
        'e_buddy_id',
        'start_datetime',
        'end_datetime',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime'   => 'datetime',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * True if right now falls within this unavailability window.
     */
    public function isActiveNow(): bool
    {
        $now = now();
        
        // Ensure we are comparing in the same timezone
        $start = $this->start_datetime->setTimezone($now->timezone);
        $end   = $this->end_datetime->setTimezone($now->timezone);

        return $now->between($start, $end);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }
}