<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'cover',
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
}