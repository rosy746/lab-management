<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password_hash',
        'full_name', 'phone', 'role', 'organization_id', 'is_active',
    ];

    protected $hidden = [];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}