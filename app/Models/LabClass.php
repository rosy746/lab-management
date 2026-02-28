<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabClass extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name', 'organization_id', 'grade_level',
        'major', 'student_count', 'academic_year', 'semester', 'is_active',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}