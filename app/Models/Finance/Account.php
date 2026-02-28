<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'finance';
    protected $table      = 'accounts';
    protected $fillable   = ['name', 'type', 'balance', 'description', 'is_active'];
    protected $casts      = ['balance' => 'decimal:2', 'is_active' => 'boolean'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
