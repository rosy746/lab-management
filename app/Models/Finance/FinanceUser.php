<?php

namespace App\Models\Finance;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FinanceUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'finance';
    protected $table      = 'finance_users';
    protected $guard      = 'finance';

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'phone', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ── Role Helpers ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'     => 'Admin',
            'bendahara' => 'Bendahara',
            default     => ucfirst($this->role),
        };
    }

    // ── Relasi ────────────────────────────────────────────

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
}
