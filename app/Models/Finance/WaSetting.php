<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class WaSetting extends Model
{
    protected $connection = 'finance';
    protected $table      = 'wa_settings';

    protected $fillable = [
        'fonnte_token',
        'admin_phone',       // legacy — tetap disimpan sebagai primary
        'target_phones',     // JSON array berisi semua target (termasuk admin_phone)
        'device_number',
        'notify_income',
        'notify_expense',
        'notify_budget_warning',
        'notify_on_transaction',
        'notify_on_budget_exceeded',
        'budget_warning_pct',
        'budget_alert_threshold',
        'is_active',
    ];

    protected $casts = [
        'target_phones'              => 'array',
        'notify_income'              => 'boolean',
        'notify_expense'             => 'boolean',
        'notify_budget_warning'      => 'boolean',
        'notify_on_transaction'      => 'boolean',
        'notify_on_budget_exceeded'  => 'boolean',
        'is_active'                  => 'boolean',
    ];

    /**
     * Ambil semua target aktif (gabungan admin_phone + target_phones).
     * Return array of unique non-empty strings.
     */
    public function getAllTargets(): array
    {
        $targets = $this->target_phones ?? [];

        // Tambahkan admin_phone legacy jika ada dan belum masuk array
        if ($this->admin_phone && !in_array($this->admin_phone, $targets)) {
            array_unshift($targets, $this->admin_phone);
        }

        return array_values(array_unique(array_filter($targets)));
    }

    /**
     * Label tampilan untuk sebuah nomor/ID target.
     */
    public static function formatTarget(string $target): string
    {
        if (str_contains($target, '@g.us')) {
            return '? Grup: ' . $target;
        }
        return '? ' . $target;
    }
}