<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $connection = 'finance';
    protected $table      = 'budgets';
    protected $fillable   = ['budget_period_id', 'category_id', 'amount', 'used_amount', 'notes'];
    protected $casts      = ['amount' => 'decimal:2', 'used_amount' => 'decimal:2'];

    public function period()
    {
        return $this->belongsTo(BudgetPeriod::class, 'budget_period_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRemainingAttribute(): float
    {
        return (float) ($this->amount - $this->used_amount);
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return round(($this->used_amount / $this->amount) * 100, 1);
    }
}
