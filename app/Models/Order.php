<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'player_id',
        'e_buddy_id',
        'service_id',
        'status',
        'total_amount',
        'hours',
        'refuse_reason',
        'expires_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'float',
            'hours'        => 'integer',
            'expires_at'   => 'datetime',
            'paid_at'      => 'datetime',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isRefused(): bool   { return $this->status === 'refused'; }
    public function isPaid(): bool      { return $this->status === 'paid'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->isPending() && $this->expires_at && now()->isAfter($this->expires_at));
    }

    public function hasSessionEnded(): bool
    {
        if (!$this->isPaid() || !$this->paid_at) return false;
        return now()->isAfter($this->paid_at->addHours($this->hours));
    }

    public function canBeCompleted(): bool
    {
        return $this->isPaid() && $this->hasSessionEnded();
    }

    // ── Action Helpers ─────────────────────────────────────────────────────

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
        
        // Create initial processing payment
        $this->payment()->updateOrCreate([], [
            'amount' => $this->total_amount,
            'status' => 'processing',
        ]);
    }

    public function refuse(string $reason): void
    {
        $this->update([
            'status'        => 'refused',
            'refuse_reason' => $reason,
        ]);
    }

    public function pay(): void
    {
        $this->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
        if ($this->payment) {
            $this->payment->update(['status' => 'succeeded']);
        }
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        if ($this->payment) {
            $this->payment->update(['status' => 'canceled']);
        }
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    // ── Ownership Helpers ──────────────────────────────────────────────────

    /**
     * True if the given user is the player who placed this order.
     */
    public function belongsToPlayer(User $user): bool
    {
        return $this->player_id === $user->id;
    }

    /**
     * True if the given user is the e-buddy assigned to this order.
     */
    public function belongsToEBuddy(User $user): bool
    {
        return $this->e_buddy_id === $user->id;
    }

    /**
     * True if the given user is either the player or the e-buddy on this order.
     */
    public function isParticipant(User $user): bool
    {
        return $this->belongsToPlayer($user) || $this->belongsToEBuddy($user);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    /**
     * e_buddies PK is user_id.
     */
    public function eBuddy(): BelongsTo
    {
        return $this->belongsTo(EBuddy::class, 'e_buddy_id', 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isReviewed(): bool
    {
        return $this->review()->exists();
    }

    /**
     * Get or create the chat room ID for this order.
     */
    public function getChatRoomId(): int
    {
        $room = ChatRoom::where(function($q) {
            $q->where('player_id', $this->player_id)->where('e_buddy_id', $this->e_buddy_id);
        })->orWhere(function($q) {
            $q->where('player_id', $this->e_buddy_id)->where('e_buddy_id', $this->player_id);
        })->first();

        if (!$room) {
            $room = ChatRoom::create([
                'player_id' => $this->player_id,
                'e_buddy_id' => $this->e_buddy_id,
            ]);
        }

        return $room->id;
    }
}