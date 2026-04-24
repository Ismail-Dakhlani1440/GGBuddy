<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    protected $fillable = [
        'game_id',
        'title',
        'tier',
        'icon',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function currentRankProfiles(): HasMany
    {
        return $this->hasMany(PlayerGameProfile::class, 'current_rank_id');
    }

    public function peakRankProfiles(): HasMany
    {
        return $this->hasMany(PlayerGameProfile::class, 'peak_rank_id');
    }

    public function matchesAsAverage(): HasMany
    {
        return $this->hasMany(Matches::class, 'average_rank_id');
    }
}