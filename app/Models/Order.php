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
        'refuse_reason',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'float',
            'expires_at'   => 'datetime',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isRefused(): bool   { return $this->status === 'refused'; }
    public function isPaid(): bool      { return $this->status === 'paid'; }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->isPending() && now()->isAfter($this->expires_at));
    }

    // ── Action Helpers ─────────────────────────────────────────────────────

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function refuse(string $reason = ''): void
    {
        $this->update([
            'status'        => 'refused',
            'refuse_reason' => $reason,
        ]);
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
}