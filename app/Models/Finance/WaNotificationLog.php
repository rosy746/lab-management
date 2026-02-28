<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class WaNotificationLog extends Model
{
    protected $connection = 'finance';
    protected $table      = 'wa_notification_logs';
    public    $timestamps = false;
    protected $fillable   = ['transaction_id', 'phone', 'message', 'status', 'response', 'sent_at'];
    protected $casts      = ['sent_at' => 'datetime', 'created_at' => 'datetime'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
