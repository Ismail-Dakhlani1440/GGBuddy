<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['title'];

    public $timestamps = false;

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isAdmin(): bool  { return $this->title === 'admin'; }
    public function isEBuddy(): bool { return $this->title === 'ebuddy'; }
    public function isPlayer(): bool { return $this->title === 'player'; }

    // ── Relations ──────────────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}