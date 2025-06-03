<?php

namespace App\Listeners;

use App\Events\AttendanceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogAttendanceActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttendanceCreated $event): void
    {
        $attendance = $event->attendance;
        $user = $attendance->user;
        $employee = $user->employee;

        Log::info('Attendance activity logged', [
            'user_id' => $user->id,
            'employee_id' => $employee->employee_id ?? null,
            'employee_name' => $user->name,
            'attendance_type' => $attendance->type,
            'attendance_date' => $attendance->attendance_date,
            'attendance_time' => $attendance->attendance_time,
            'status' => $attendance->status,
            'is_late' => $attendance->is_late,
            'late_minutes' => $attendance->late_minutes,
            'location' => $attendance->location->name ?? null,
            'distance_from_office' => $attendance->distance_from_office,
            'face_similarity_score' => $attendance->face_similarity_score,
            'device_info' => $attendance->device_info,
            'ip_address' => $attendance->ip_address
        ]);

        // Log specific events
        if ($attendance->status === 'failed') {
            Log::warning('Attendance attempt failed', [
                'user_id' => $user->id,
                'employee_name' => $user->name,
                'type' => $attendance->type,
                'failure_reason' => $attendance->failure_reason,
                'face_score' => $attendance->face_similarity_score,
                'location_valid' => $attendance->is_valid_location
            ]);
        }

        if ($attendance->is_late && $attendance->type === 'clock_in') {
            Log::info('Late attendance recorded', [
                'user_id' => $user->id,
                'employee_name' => $user->name,
                'late_minutes' => $attendance->late_minutes,
                'attendance_time' => $attendance->attendance_time,
                'scheduled_time' => $employee->work_start_time ?? null
            ]);
        }

        // Log security concerns
        if ($attendance->face_similarity_score < 0.8 && $attendance->status === 'success') {
            Log::warning('Low face similarity score but attendance approved', [
                'user_id' => $user->id,
                'employee_name' => $user->name,
                'similarity_score' => $attendance->face_similarity_score,
                'threshold' => config('services.biznet_face.similarity_threshold', 0.75)
            ]);
        }

        if (!$attendance->is_valid_location && $attendance->status === 'success') {
            Log::warning('Attendance approved outside valid location', [
                'user_id' => $user->id,
                'employee_name' => $user->name,
                'distance' => $attendance->distance_from_office,
                'location' => $attendance->location->name ?? null
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(AttendanceCreated $event, $exception): void
    {
        Log::error('Failed to log attendance activity', [
            'attendance_id' => $event->attendance->id,
            'error' => $exception->getMessage()
        ]);
    }
}
