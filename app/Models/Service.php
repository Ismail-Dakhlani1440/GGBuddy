<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'e_buddy_id',
        'game_id',
        'title',
        'description',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────

    /**
     * e_buddies PK is user_id — must specify both FK and owner key.
     */
    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getRankAttribute()
    {
        $profile = PlayerGameProfile::where('user_id', $this->e_buddy_id)
            ->where('game_id', $this->game_id)
            ->first();

        return $profile ? ($profile->currentRank ?? $profile->peakRank) : null;
    }
}