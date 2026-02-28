<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'finance';
    protected $table      = 'categories';
    protected $fillable   = ['name', 'type', 'color', 'icon', 'description', 'is_active'];
    protected $casts      = ['is_active' => 'boolean'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeIncome($q)  { return $q->where('type', 'income'); }
    public function scopeExpense($q) { return $q->where('type', 'expense'); }
}
