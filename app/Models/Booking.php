<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'resource_id', 'time_slot_id', 'user_id', 'organization_id',
        'booking_date', 'teacher_name', 'teacher_phone',
        'class_name', 'subject_name', 'title', 'description',
        'participant_count', 'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'approved_at'  => 'datetime',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}