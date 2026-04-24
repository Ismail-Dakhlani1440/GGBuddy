<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'amount',
        'status',
        'payment_method',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isSucceeded(): bool { return $this->status === 'succeeded'; }
    public function isFailed(): bool    { return $this->status === 'failed'; }
    public function isCanceled(): bool  { return $this->status === 'canceled'; }

    public function isPending(): bool
    {
        return in_array($this->status, [
            'requires_payment_method',
            'requires_action',
            'processing',
        ]);
    }

    /**
     * True if the payment was successful and the order can proceed.
     */
    public function isCleared(): bool
    {
        return $this->isSucceeded();
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}