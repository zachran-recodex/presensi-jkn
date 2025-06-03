<?php

namespace App\Listeners;

use App\Events\FaceEnrolled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogFaceEnrollment implements ShouldQueue
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
    public function handle(FaceEnrolled $event): void
    {
        $user = $event->user;
        $enrolledBy = $event->enrolledBy;
        $employee = $user->employee;

        Log::info('Face enrollment completed', [
            'user_id' => $user->id,
            'employee_id' => $employee->employee_id ?? null,
            'employee_name' => $user->name,
            'face_id' => $event->faceId,
            'enrolled_by_id' => $enrolledBy->id,
            'enrolled_by_name' => $enrolledBy->name,
            'enrolled_at' => now(),
            'user_email' => $user->email,
            'employee_department' => $employee->department ?? null,
            'employee_position' => $employee->position ?? null
        ]);

        // Security audit log
        Log::channel('audit')->info('FACE_ENROLLMENT', [
            'action' => 'face_enrolled',
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'actor_user_id' => $enrolledBy->id,
            'actor_user_name' => $enrolledBy->name,
            'face_id' => $event->faceId,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(FaceEnrolled $event, $exception): void
    {
        Log::error('Failed to log face enrollment', [
            'user_id' => $event->user->id,
            'face_id' => $event->faceId,
            'enrolled_by_id' => $event->enrolledBy->id,
            'error' => $exception->getMessage()
        ]);
    }
}
