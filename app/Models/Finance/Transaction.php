<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $connection = 'finance';
    protected $table      = 'transactions';
    protected $fillable   = [
        'code', 'account_id', 'category_id', 'budget_period_id',
        'type', 'amount', 'transaction_date', 'description',
        'notes', 'reference', 'attachment', 'created_by', 'created_by_name',
    ];
    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function account()      { return $this->belongsTo(Account::class); }
    public function category()     { return $this->belongsTo(Category::class); }
    public function budgetPeriod() { return $this->belongsTo(BudgetPeriod::class); }
    public function createdBy()    { return $this->belongsTo(FinanceUser::class, 'created_by'); }

    public function scopeIncome($q)  { return $q->where('type', 'income'); }
    public function scopeExpense($q) { return $q->where('type', 'expense'); }

    public function scopeByPeriod($q, int $month, int $year)
    {
        return $q->whereMonth('transaction_date', $month)
                 ->whereYear('transaction_date', $year);
    }

    public static function generateCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "TRX-{$date}-";
        $last   = static::withTrashed()->where('code', 'like', "{$prefix}%")->orderByDesc('code')->value('code');
        $seq    = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
