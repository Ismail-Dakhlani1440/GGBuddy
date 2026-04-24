<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Matches extends Model
{
    protected $table = 'matches';

    public $timestamps = false;

    protected $fillable = [
        'game_id',
        'average_rank_id',
        'status',
        'created_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'ended_at'   => 'datetime',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function isFinished(): bool { return $this->status === 'finished'; }

    // ── Action Helpers ─────────────────────────────────────────────────────

    public function start(): void
    {
        $this->update(['status' => 'active']);
    }

    public function finish(): void
    {
        $this->update([
            'status'   => 'finished',
            'ended_at' => now(),
        ]);
    }

    /**
     * Duration in minutes — null if match hasn't finished yet.
     */
    public function durationInMinutes(): ?int
    {
        if (! $this->ended_at) {
            return null;
        }
        return (int) $this->created_at->diffInMinutes($this->ended_at);
    }

    /**
     * True if the given user is a participant of this match.
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->participations()->where('user_id', $userId)->exists();
    }

    /**
     * Add a user to this match — prevents duplicates.
     */
    public function addParticipant(User $user): MatchParticipation
    {
        return $this->participations()->firstOrCreate(
            ['user_id' => $user->id],
            ['joined_at' => now()]
        );
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function averageRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'average_rank_id');
    }

    public function participations(): HasMany
    {
        return $this->hasMany(MatchParticipation::class, 'match_id');
    }

    /**
     * Direct access to users in this match through participations.
     */
    public function players(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            MatchParticipation::class,
            'match_id',  // FK on match_participations
            'id',        // FK on users
            'id',        // local key on matches
            'user_id'    // local key on match_participations
        );
    }
}