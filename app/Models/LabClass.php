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
        'pin',  // ← tambah ini
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    // ─── PIN HELPERS ──────────────────────────────────────────────

    /**
     * Generate PIN unik 6 digit
     */
    public static function generateUniquePin(): string
    {
        do {
            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('pin', $pin)->exists());

        return $pin;
    }

    /**
     * Scope cari kelas berdasarkan PIN
     */
    public function scopeByPin($query, string $pin)
    {
        return $query->where('pin', $pin)
                     ->where('is_active', true)
                     ->whereNull('deleted_at');
    }
}