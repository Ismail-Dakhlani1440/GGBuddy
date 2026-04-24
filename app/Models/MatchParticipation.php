<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchParticipation extends Model
{
    protected $fillable = [
        'match_id',
        'user_id',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * How long ago this user joined the match, as a human-readable string.
     * e.g. "3 minutes ago"
     */
    public function joinedAgo(): string
    {
        return $this->joined_at->diffForHumans();
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}