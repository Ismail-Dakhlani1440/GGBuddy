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
        return now()->between($this->start_datetime, $this->end_datetime);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }
}