<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_id',
        'player_id',
        'e_buddy_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Returns rating as filled/empty stars e.g. "★★★☆☆"
     */
    public function starsLabel(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Returns rating as boolean array for frontend star rendering.
     * e.g. rating=3 → [true, true, true, false, false]
     */
    public function starsArray(): array
    {
        return array_map(fn(int $i) => $i <= $this->rating, range(1, 5));
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

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
}