<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EBuddy extends Model
{
    protected $table      = 'e_buddies';
    protected $primaryKey = 'user_id';
    public    $incrementing = false;

    protected $fillable = [
        'user_id',
        'status',
        'bio',
        'banner',
        'global_rating',
        'missed_order_count',
    ];

    protected function casts(): array
    {
        return [
            'global_rating'      => 'float',
            'missed_order_count' => 'integer',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }

    // ── Rating Helpers ─────────────────────────────────────────────────────

    public function refreshGlobalRating(): void
    {
        $avg = round($this->reviews()->avg('rating') ?? 0, 2);
        $this->update(['global_rating' => $avg]);
    }

    public function getSessionCount(): int
    {
        return $this->orders()->where('status', 'completed')->count();
    }

    public function getCompletionRate(): int
    {
        $total = $this->orders()->whereIn('status', ['completed', 'refused', 'expired', 'cancelled'])->count();
        if ($total === 0) return 100;

        $completed = $this->orders()->where('status', 'completed')->count();
        return (int) (($completed / $total) * 100);
    }

    public function getTotalEarnings(): float
    {
        return (float) $this->orders()->where('status', 'paid')->sum('total_amount');
    }

    // ── Availability Helpers ───────────────────────────────────────────────
    public function isAvailableNow(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $hasActiveSlot = $this->scheduals->contains(
            fn(Schedual $s) => $s->isActiveNow()
        );

        $isUnavailable = $this->unavailabilities->contains(
            fn(Unavailability $u) => $u->isActiveNow()
        );

        return $hasActiveSlot && ! $isUnavailable;
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'e_buddy_id', 'user_id');
    }

    public function scheduals(): HasMany
    {
        return $this->hasMany(Schedual::class, 'e_buddy_id', 'user_id');
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(Unavailability::class, 'e_buddy_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'e_buddy_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'e_buddy_id', 'user_id');
    }

    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'e_buddy_id', 'user_id');
    }
}