<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LabSession extends Model
{
    protected $fillable = [
        'token', 'lab_key', 'resource_id', 'source_type', 'source_id',
        'teacher_name', 'teacher_phone', 'session_start', 'session_end',
        'used_at', 'invalidated_at', 'is_active', 'invalidated_reason'
    ];

    protected $casts = [
        'session_start'    => 'datetime',
        'session_end'      => 'datetime',
        'used_at'          => 'datetime',
        'invalidated_at'   => 'datetime',
        'is_active'        => 'boolean',
    ];

    // Lab key mapping
    const LAB_MAP = [
        'lab7'   => ['name' => 'Lab Komputer 7',   'resource_id' => 1],
        'lab8'   => ['name' => 'Lab Komputer 8',   'resource_id' => 2],
        'lab1'   => ['name' => 'Lab Komputer 1',   'resource_id' => 3],
        'lab2'   => ['name' => 'Lab Komputer 2',   'resource_id' => 4],
        'labsmp' => ['name' => 'Lab Komputer SMP', 'resource_id' => 5],
        'lab3'   => ['name' => 'Lab Komputer 3',   'resource_id' => 6],
        'lab4'   => ['name' => 'Lab Komputer 4',   'resource_id' => 7],
        'labfo'  => ['name' => 'Lab Fiber Optic',  'resource_id' => 8],
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    // Generate token unik 8 karakter
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(4) . '-' . Str::random(4));
        } while (self::where('token', $token)->exists());

        return $token;
    }

    // Cek apakah token masih valid
    public function isValid(): bool
    {
        return $this->is_active
            && $this->invalidated_at === null
            && now()->between($this->session_start, $this->session_end);
    }

    // Cek apakah token sudah expired
    public function isExpired(): bool
    {
        return now()->isAfter($this->session_end);
    }

    // Tandai token sudah dipakai pertama kali
    public function markAsUsed(): void
    {
        if ($this->used_at === null) {
            $this->update(['used_at' => now()]);
        }
    }

    // Hanguskan token
    public function invalidate(string $reason = 'logout'): void
    {
        $this->update([
            'is_active'          => false,
            'invalidated_at'     => now(),
            'invalidated_reason' => $reason,
        ]);
    }

    // Get lab name
    public function getLabNameAttribute(): string
    {
        return self::LAB_MAP[$this->lab_key]['name'] ?? $this->lab_key;
    }

    // Scope aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereNull('invalidated_at')
                     ->where('session_end', '>', now());
    }
}