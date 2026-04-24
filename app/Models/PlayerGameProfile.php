<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerGameProfile extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'current_rank_id',
        'peak_rank_id',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * True if player has improved beyond their recorded peak.
     */
    public function hasNewPeak(): bool
    {
        if (! $this->currentRank || ! $this->peakRank) {
            return false;
        }
        return $this->currentRank->tier > $this->peakRank->tier;
    }

    /**
     * Update peak rank if current rank tier is higher.
     */
    public function syncPeakRank(): void
    {
        if ($this->hasNewPeak()) {
            $this->update(['peak_rank_id' => $this->current_rank_id]);
        }
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function currentRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'current_rank_id');
    }

    public function peakRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'peak_rank_id');
    }
}