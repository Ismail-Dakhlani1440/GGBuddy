<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    // ── Status Helpers ─────────────────────────────────────────────────────

    public function isSucceeded(): bool  { return $this->status === 'succeeded'; }
    public function isFailed(): bool     { return $this->status === 'failed'; }
    public function isCanceled(): bool   { return $this->status === 'canceled'; }
    public function isProcessing(): bool { return $this->status === 'processing'; }

    // ── Relations ──────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}