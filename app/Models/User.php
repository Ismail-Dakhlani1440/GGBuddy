<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id',
        'name',
        'display_name',
        'avatar',
        'timezone',
        'email',
        'password',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ── Role Helpers ───────────────────────────────────────────────────────

    public function isAdmin(): bool  { return $this->role->title === 'admin'; }
    public function isEBuddy(): bool { return $this->role->title === 'ebuddy'; }
    public function isPlayer(): bool { return $this->role->title === 'player'; }

    // ── E-Buddy shortcut helpers ───────────────────────────────────────────

    /**
     * Get the active e-buddy profile or abort with 403.
     * Use in controllers: $request->user()->eBuddyOrFail()
     */
    public function eBuddyOrFail(): EBuddy
    {
        abort_unless($this->isEBuddy() && $this->eBuddy?->isActive(), 403, 'Forbidden.');
        return $this->eBuddy;
    }

    /**
     * Shortcut to check if this user owns a given e-buddy record.
     */
    public function ownsEBuddy(EBuddy $eBuddy): bool
    {
        return $this->id === $eBuddy->user_id;
    }

    /**
     * Shortcut to check if this user placed a given order.
     */
    public function ownsOrder(Order $order): bool
    {
        return $this->id === $order->player_id;
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * e_buddies.user_id is the PK so we use the foreign key explicitly.
     */
    public function eBuddy(): HasOne
    {
        return $this->hasOne(EBuddy::class, 'user_id', 'id');
    }

    public function gameProfiles(): HasMany
    {
        return $this->hasMany(PlayerGameProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'player_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'player_id');
    }

    public function reportsMade(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsReceived(): HasMany
    {
        return $this->hasMany(Report::class, 'target_id');
    }

    public function matchParticipations(): HasMany
    {
        return $this->hasMany(MatchParticipation::class);
    }

    public function matchmakingQueues(): HasMany
    {
        return $this->hasMany(MatchmakingQueue::class, 'player_id');
    }

    public function chatRoomsAsPlayer(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'player_id');
    }

    public function chatRoomsAsEBuddy(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'e_buddy_id');
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }
}
