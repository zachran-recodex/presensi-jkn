<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceHelper
{
    /**
     * Calculate working days in a month (excluding weekends)
     */
    public static function getWorkingDaysInMonth(Carbon $month): int
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $workingDays = 0;

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    /**
     * Calculate working hours between two timestamps
     */
    public static function calculateWorkingHours(string $clockIn, string $clockOut): float
    {
        $startTime = Carbon::parse($clockIn);
        $endTime = Carbon::parse($clockOut);

        return $startTime->diffInHours($endTime, true);
    }

    /**
     * Calculate late minutes based on scheduled work time
     */
    public static function calculateLateMinutes(string $clockInTime, string $scheduledStartTime): int
    {
        $clockIn = Carbon::parse($clockInTime);
        $scheduled = Carbon::parse($scheduledStartTime);

        if ($clockIn->greaterThan($scheduled)) {
            return $clockIn->diffInMinutes($scheduled);
        }

        return 0;
    }

    /**
     * Get attendance status text in Indonesian
     */
    public static function getAttendanceStatusText(string $status): string
    {
        return match($status) {
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'pending' => 'Menunggu',
            default => ucfirst($status),
        };
    }

    /**
     * Get attendance type text in Indonesian
     */
    public static function getAttendanceTypeText(string $type): string
    {
        return match($type) {
            'clock_in' => 'Masuk',
            'clock_out' => 'Pulang',
            default => ucfirst($type),
        };
    }

    /**
     * Format time for display
     */
    public static function formatTime($time, string $format = 'H:i'): string
    {
        if (!$time) {
            return '-';
        }

        return Carbon::parse($time)->format($format);
    }

    /**
     * Format date for display
     */
    public static function formatDate($date, string $format = 'd/m/Y'): string
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->format($format);
    }

    /**
     * Get attendance summary for employee in a month
     */
    public static function getMonthlyAttendanceSummary(Employee $employee, Carbon $month): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $employee->user_id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->get()
            ->groupBy('attendance_date');

        $summary = [
            'total_days' => $attendances->count(),
            'present_days' => 0,
            'late_days' => 0,
            'total_work_hours' => 0,
            'average_work_hours' => 0,
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
                    $workHours = self::calculateWorkingHours(
                        $clockIn->attendance_time,
                        $clockOut->attendance_time
                    );
                    $summary['total_work_hours'] += $workHours;
                }
            }
        }

        // Calculate average work hours
        if ($summary['present_days'] > 0) {
            $summary['average_work_hours'] = $summary['total_work_hours'] / $summary['present_days'];
        }

        return $summary;
    }

    /**
     * Check if current time is within work hours for employee
     */
    public static function isWithinWorkHours(Employee $employee): bool
    {
        if ($employee->is_flexible_time) {
            return true;
        }

        $now = Carbon::now();
        $startTime = Carbon::parse($employee->work_start_time);
        $endTime = Carbon::parse($employee->work_end_time);

        return $now->between($startTime, $endTime);
    }

    /**
     * Get next expected action for employee (clock_in or clock_out)
     */
    public static function getNextExpectedAction($user): ?string
    {
        if (!$user->employee || $user->employee->status !== 'active') {
            return null;
        }

        $hasClockedIn = $user->hasClockedInToday();
        $hasClockedOut = $user->hasClockedOutToday();

        if (!$hasClockedIn) {
            return 'clock_in';
        }

        if ($hasClockedIn && !$hasClockedOut) {
            return 'clock_out';
        }

        return null; // Already completed for the day
    }

    /**
     * Calculate attendance rate percentage
     */
    public static function calculateAttendanceRate(int $presentDays, int $totalWorkingDays): float
    {
        if ($totalWorkingDays === 0) {
            return 0;
        }

        return round(($presentDays / $totalWorkingDays) * 100, 2);
    }

    /**
     * Get attendance statistics for dashboard
     */
    public static function getDashboardStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();

        return [
            'today' => [
                'total_present' => Attendance::whereDate('attendance_date', $today)
                    ->where('type', 'clock_in')
                    ->where('status', 'success')
                    ->count(),
                'total_late' => Attendance::whereDate('attendance_date', $today)
                    ->where('type', 'clock_in')
                    ->where('status', 'success')
                    ->where('is_late', true)
                    ->count(),
            ],
            'this_month' => [
                'working_days' => self::getWorkingDaysInMonth($thisMonth),
                'total_attendance' => Attendance::whereMonth('attendance_date', $thisMonth->month)
                    ->whereYear('attendance_date', $thisMonth->year)
                    ->where('type', 'clock_in')
                    ->where('status', 'success')
                    ->count(),
            ]
        ];
    }

    /**
     * Validate if employee can perform attendance action
     */
    public static function canPerformAttendance($user, string $type): array
    {
        $result = [
            'can_perform' => false,
            'reason' => null
        ];

        // Check if user has employee profile
        if (!$user->employee) {
            $result['reason'] = 'Profil karyawan tidak ditemukan';
            return $result;
        }

        // Check if employee is active
        if ($user->employee->status !== 'active') {
            $result['reason'] = 'Akun karyawan tidak aktif';
            return $result;
        }

        // Check if user account is active
        if (!$user->is_active) {
            $result['reason'] = 'Akun pengguna tidak aktif';
            return $result;
        }

        // Check if face is enrolled
        if (!$user->hasFaceEnrolled()) {
            $result['reason'] = 'Wajah belum didaftarkan';
            return $result;
        }

        // Check specific action
        if ($type === 'clock_in') {
            if ($user->hasClockedInToday()) {
                $result['reason'] = 'Sudah melakukan clock in hari ini';
                return $result;
            }
        }

        if ($type === 'clock_out') {
            if (!$user->hasClockedInToday()) {
                $result['reason'] = 'Belum melakukan clock in hari ini';
                return $result;
            }

            if ($user->hasClockedOutToday()) {
                $result['reason'] = 'Sudah melakukan clock out hari ini';
                return $result;
            }
        }

        $result['can_perform'] = true;
        return $result;
    }
}
