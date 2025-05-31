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
        'type',
        'photo_path',
        'latitude',
        'longitude',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include attendance from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope a query to only include attendance from this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include attendance from this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope a query to only include clock in records.
     */
    public function scopeClockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope a query to only include clock out records.
     */
    public function scopeClockOut($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope a query to only include successful attendances.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed attendances.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for late attendance (clock in after 8:00 AM).
     */
    public function scopeLate($query)
    {
        return $query->where('type', 'in')
            ->whereTime('created_at', '>', '08:00:00');
    }

    /**
     * Scope for on-time attendance (clock in before or at 8:00 AM).
     */
    public function scopeOnTime($query)
    {
        return $query->where('type', 'in')
            ->whereTime('created_at', '<=', '08:00:00');
    }

    /**
     * Check if this attendance record is late.
     *
     * @return bool
     */
    public function isLate(): bool
    {
        if ($this->type !== 'in') {
            return false;
        }

        return $this->created_at->format('H:i:s') > '08:00:00';
    }

    /**
     * Check if this attendance record is on time.
     *
     * @return bool
     */
    public function isOnTime(): bool
    {
        if ($this->type !== 'in') {
            return true; // Clock out doesn't have time restrictions
        }

        return $this->created_at->format('H:i:s') <= '08:00:00';
    }

    /**
     * Get formatted coordinates.
     *
     * @return string
     */
    public function getFormattedLocationAttribute(): string
    {
        return "({$this->latitude}, {$this->longitude})";
    }

    /**
     * Get attendance time in readable format.
     *
     * @return string
     */
    public function getTimeAttribute(): string
    {
        return $this->created_at->format('H:i:s');
    }

    /**
     * Get attendance date in readable format.
     *
     * @return string
     */
    public function getDateAttribute(): string
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Get full photo URL.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }

    /**
     * Get status badge class for UI.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'success' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get type badge class for UI.
     *
     * @return string
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'in' => 'bg-blue-100 text-blue-800',
            'out' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get working hours if both clock in and out exist for the same day.
     *
     * @return float|null Hours worked
     */
    public function getWorkingHours(): ?float
    {
        if ($this->type !== 'in') {
            return null;
        }

        $clockOut = self::where('user_id', $this->user_id)
            ->where('type', 'out')
            ->whereDate('created_at', $this->created_at->toDateString())
            ->where('created_at', '>', $this->created_at)
            ->first();

        if (!$clockOut) {
            return null;
        }

        return $this->created_at->diffInHours($clockOut->created_at, true);
    }
}
