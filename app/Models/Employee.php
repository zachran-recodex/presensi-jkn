<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Employee extends Model
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
        'employee_id',
        'phone',
        'position',
        'department',
        'join_date',
        'work_start_time',
        'work_end_time',
        'is_flexible_time',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i',
        'is_flexible_time' => 'boolean',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location where employee works.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get all attendances for this employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }

    /**
     * Scope to get only active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get employees by department.
     */
    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Check if employee is late for work based on current time.
     */
    public function isLateToday(): bool
    {
        if ($this->is_flexible_time) {
            return false;
        }

        $now = Carbon::now();
        $workStartTime = Carbon::parse($this->work_start_time);

        return $now->greaterThan($workStartTime);
    }

    /**
     * Calculate late minutes for today.
     */
    public function getLateMinutesToday(): int
    {
        if ($this->is_flexible_time) {
            return 0;
        }

        $now = Carbon::now();
        $workStartTime = Carbon::parse($this->work_start_time);

        if ($now->greaterThan($workStartTime)) {
            return $now->diffInMinutes($workStartTime);
        }

        return 0;
    }

    /**
     * Get work duration in hours.
     */
    public function getWorkDurationHours(): float
    {
        $startTime = Carbon::parse($this->work_start_time);
        $endTime = Carbon::parse($this->work_end_time);

        return $startTime->diffInHours($endTime, true);
    }

    /**
     * Check if current time is within work hours.
     */
    public function isWithinWorkHours(): bool
    {
        $now = Carbon::now();
        $startTime = Carbon::parse($this->work_start_time);
        $endTime = Carbon::parse($this->work_end_time);

        return $now->between($startTime, $endTime);
    }

    /**
     * Get attendance summary for a specific month.
     */
    public function getMonthlyAttendanceSummary(int $year, int $month): array
    {
        $attendances = $this->attendances()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->where('status', 'success')
            ->get()
            ->groupBy('attendance_date');

        $summary = [
            'total_days' => $attendances->count(),
            'present_days' => 0,
            'late_days' => 0,
            'total_work_hours' => 0,
        ];

        foreach ($attendances as $date => $dayAttendances) {
            $clockIn = $dayAttendances->where('type', 'clock_in')->first();
            $clockOut = $dayAttendances->where('type', 'clock_out')->first();

            if ($clockIn) {
                $summary['present_days']++;

                if ($clockIn->is_late) {
                    $summary['late_days']++;
                }

                if ($clockIn && $clockOut) {
                    $startTime = Carbon::parse($clockIn->attendance_time);
                    $endTime = Carbon::parse($clockOut->attendance_time);
                    $summary['total_work_hours'] += $startTime->diffInHours($endTime, true);
                }
            }
        }

        return $summary;
    }
}
