<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    protected $fillable = [
        'player_id',
        'e_buddy_id',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    public function hasParticipant(int $userId): bool
    {
        return $this->player_id === $userId
            || $this->e_buddy_id === $userId;
    }


    public function authorizeParticipant(User $user): void
    {
        abort_unless($this->hasParticipant($user->id), 403, 'You are not a participant of this chat room.');
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'e_buddy_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('sent_at');
    }

    /**
     * Latest message — useful for listing chat rooms with previews.
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest('sent_at')->limit(1);
    }
}