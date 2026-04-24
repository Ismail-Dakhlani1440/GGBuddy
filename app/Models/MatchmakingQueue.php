<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchmakingQueue extends Model
{
    protected $fillable = [
        'player_id',
        'game_id',
        'status',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isSearching(): bool  { return $this->status === 'searching'; }
    public function isMatched(): bool    { return $this->status === 'matched'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsMatched(): void
    {
        $this->update(['status' => 'matched']);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}