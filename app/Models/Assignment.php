<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'teacher_id', 'organization_id', 'title', 'description',
        'subject_name', 'class_name', 'deadline', 'is_active',
        'attachment_path', 'attachment_name', 'attachment_size'
    ];

    protected $casts = [
        'deadline'  => 'datetime',
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isExpired(): bool
    {
        return $this->deadline->isPast();
    }
}

