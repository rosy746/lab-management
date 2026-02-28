<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class BudgetPeriod extends Model
{
    protected $connection = 'finance';
    protected $table      = 'budget_periods';
    protected $fillable   = ['name', 'month', 'year', 'total_budget', 'notes', 'status', 'created_by'];
    protected $casts      = ['total_budget' => 'decimal:2'];

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'expense')->whereNull('deleted_at')->sum('amount');
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'income')->whereNull('deleted_at')->sum('amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->total_budget - $this->total_expense;
    }

    public static function generateName(int $month, int $year): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
            5 => 'Mei',     6 => 'Juni',     7 => 'Juli',      8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return "{$months[$month]} {$year}";
    }

    public static function currentPeriod(): ?self
    {
        return static::where('month', now()->month)
                     ->where('year', now()->year)
                     ->first();
    }
}
