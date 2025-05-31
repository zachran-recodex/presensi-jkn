<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'face_id',
        'is_face_enrolled',
        'status',
        'location_id',
        'employee_id',
        'phone',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_face_enrolled' => 'boolean',
    ];

    /**
     * Get the employee record associated with the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get the attendances for the user.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the location assigned to the user.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user (satpam)
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has face enrolled
     *
     * @return bool
     */
    public function hasFaceEnrolled(): bool
    {
        return $this->is_face_enrolled && !empty($this->face_id);
    }

    /**
     * Scope untuk active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope untuk admin users
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope untuk regular users
     */
    public function scopeRegularUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Get today's attendance records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayAttendances()
    {
        return $this->attendances()
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Check if user has clocked in today.
     *
     * @return bool
     */
    public function hasClockInToday(): bool
    {
        return $this->attendances()
            ->where('type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }

    /**
     * Check if user has clocked out today.
     *
     * @return bool
     */
    public function hasClockOutToday(): bool
    {
        return $this->attendances()
            ->where('type', 'out')
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }

    /**
     * Get user's last attendance record.
     *
     * @return Attendance|null
     */
    public function getLastAttendance(): ?Attendance
    {
        return $this->attendances()
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get today's clock in time.
     *
     * @return Carbon|null
     */
    public function getTodayClockIn(): ?Carbon
    {
        $attendance = $this->attendances()
            ->where('type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->first();

        return $attendance ? $attendance->created_at : null;
    }

    /**
     * Get today's clock out time.
     *
     * @return Carbon|null
     */
    public function getTodayClockOut(): ?Carbon
    {
        $attendance = $this->attendances()
            ->where('type', 'out')
            ->whereDate('created_at', Carbon::today())
            ->first();

        return $attendance ? $attendance->created_at : null;
    }

    /**
     * Check if user was late today.
     *
     * @return bool
     */
    public function isLateToday(): bool
    {
        $clockIn = $this->getTodayClockIn();

        if (!$clockIn) {
            return false;
        }

        return $clockIn->format('H:i:s') > '08:00:00';
    }

    /**
     * Get monthly attendance statistics.
     *
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public function getMonthlyStats(?int $month = null, ?int $year = null): array
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;

        $attendances = $this->attendances()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $clockIns = $attendances->where('type', 'in');
        $clockOuts = $attendances->where('type', 'out');

        return [
            'total_attendance' => $attendances->count(),
            'total_days_present' => $clockIns->count(),
            'total_clock_ins' => $clockIns->count(),
            'total_clock_outs' => $clockOuts->count(),
            'late_count' => $clockIns->filter(function ($attendance) {
                return $attendance->created_at->format('H:i:s') > '08:00:00';
            })->count(),
            'on_time_count' => $clockIns->filter(function ($attendance) {
                return $attendance->created_at->format('H:i:s') <= '08:00:00';
            })->count(),
        ];
    }

    /**
     * Get working hours for a specific date.
     *
     * @param Carbon $date
     * @return float|null
     */
    public function getWorkingHours(Carbon $date): ?float
    {
        $clockIn = $this->attendances()
            ->where('type', 'in')
            ->whereDate('created_at', $date->toDateString())
            ->first();

        $clockOut = $this->attendances()
            ->where('type', 'out')
            ->whereDate('created_at', $date->toDateString())
            ->first();

        if (!$clockIn || !$clockOut) {
            return null;
        }

        return $clockIn->created_at->diffInHours($clockOut->created_at, true);
    }

    /**
     * Get user initials for avatar.
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get status badge class for UI.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get role badge class for UI.
     *
     * @return string
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'bg-purple-100 text-purple-800',
            'user' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Mark face as enrolled.
     *
     * @param string $faceId
     * @return bool
     */
    public function enrollFace(string $faceId): bool
    {
        return $this->update([
            'face_id' => $faceId,
            'is_face_enrolled' => true,
        ]);
    }

    /**
     * Remove face enrollment.
     *
     * @return bool
     */
    public function removeFaceEnrollment(): bool
    {
        return $this->update([
            'face_id' => null,
            'is_face_enrolled' => false,
        ]);
    }
}
