<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    protected $table = 'procurement_requests';

    protected $fillable = [
        'lab_id', 'item_name', 'category', 'quantity',
        'estimated_price', 'priority', 'justification',
        'specifications', 'preferred_brand', 'notes',
        'status', 'requested_by', 'requested_at',
        'reviewed_by', 'reviewed_at', 'review_notes',
        'completed_at', 'procurement_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lab()
    {
        return $this->belongsTo(Resource::class, 'lab_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}