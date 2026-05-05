<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'content',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * True if the message was sent by the given user.
     */
    public function isSentBy(User $user): bool
    {
        return $this->sender_id === $user->id;
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}