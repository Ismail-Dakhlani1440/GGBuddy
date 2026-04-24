<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Return ranks ordered by tier ascending (Bronze → Diamond).
     */
    public function ranksOrdered()
    {
        return $this->ranks()->orderBy('tier');
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function playerGameProfiles(): HasMany
    {
        return $this->hasMany(PlayerGameProfile::class);
    }

    public function matchmakingQueues(): HasMany
    {
        return $this->hasMany(MatchmakingQueue::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class);
    }
}