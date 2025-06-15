<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'location_id',
        'type',
        'attendance_date',
        'attendance_time',
        'photo_path',
        'latitude',
        'longitude',
        'is_valid_location',
        'distance_from_office',
        'status',
        'device_info',
        'ip_address',
        'face_recognition_result',
        'face_similarity_score',
        'is_late',
        'late_minutes',
        'notes',
        'failure_reason',
        'attempt_number',
        'total_attempts_today',
        'attempt_history',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attendance_date' => 'date',
        'attendance_time' => 'datetime:H:i:s',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_valid_location' => 'boolean',
        'distance_from_office' => 'decimal:2',
        'face_recognition_result' => 'array',
        'face_similarity_score' => 'decimal:2',
        'is_late' => 'boolean',
        'late_minutes' => 'integer',
        'attempt_number' => 'integer',
        'total_attempts_today' => 'integer',
        'attempt_history' => 'array',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location where attendance was recorded.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Scope to get only successful attendances.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get attendances by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get attendances for today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    /**
     * Scope to get attendances for specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Scope to get attendances within date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted attendance time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->attendance_time)->format('H:i:s');
    }

    /**
     * Get formatted attendance date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->attendance_date->format('d/m/Y');
    }

    /**
     * Get attendance type in Indonesian.
     */
    public function getTypeIndonesianAttribute(): string
    {
        return match($this->type) {
            'clock_in' => 'Masuk',
            'clock_out' => 'Pulang',
            default => $this->type,
        };
    }

    /**
     * Get status in Indonesian.
     */
    public function getStatusIndonesianAttribute(): string
    {
        return match($this->status) {
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'pending' => 'Pending',
            default => $this->status,
        };
    }

    /**
     * Check if attendance is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if attendance is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if this is a clock in attendance.
     */
    public function isClockIn(): bool
    {
        return $this->type === 'clock_in';
    }

    /**
     * Check if this is a clock out attendance.
     */
    public function isClockOut(): bool
    {
        return $this->type === 'clock_out';
    }

    /**
     * Calculate work duration from clock in to clock out.
     */
    public function getWorkDuration(): ?float
    {
        if ($this->type !== 'clock_out') {
            return null;
        }

        $clockIn = self::where('user_id', $this->user_id)
            ->where('attendance_date', $this->attendance_date)
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->first();

        if (!$clockIn) {
            return null;
        }

        $startTime = Carbon::parse($clockIn->attendance_time);
        $endTime = Carbon::parse($this->attendance_time);

        return $startTime->diffInHours($endTime, true);
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path);
    }

    /**
     * Check if face recognition was successful.
     */
    public function isFaceRecognitionSuccessful(): bool
    {
        return !empty($this->face_recognition_result) &&
            $this->face_similarity_score >= 0.75; // Default threshold
    }

    /**
     * Get late status text.
     */
    public function getLateStatusText(): string
    {
        if (!$this->is_late) {
            return 'Tepat Waktu';
        }

        return "Terlambat {$this->late_minutes} menit";
    }
}
