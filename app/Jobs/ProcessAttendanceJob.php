<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\FaceRecognitionService;
use App\Services\ImageProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attendanceId;
    protected $base64Photo;
    protected $faceId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job should run.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(int $attendanceId, string $base64Photo, string $faceId)
    {
        $this->attendanceId = $attendanceId;
        $this->base64Photo = $base64Photo;
        $this->faceId = $faceId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        FaceRecognitionService $faceService,
        ImageProcessingService $imageService
    ): void
    {
        try {
            $attendance = Attendance::find($this->attendanceId);

            if (!$attendance) {
                Log::error("Attendance record not found: {$this->attendanceId}");
                return;
            }

            Log::info("Processing attendance for user {$attendance->user_id}, type: {$attendance->type}");

            // Process face verification
            $faceResult = $this->processFaceVerification($faceService);

            // Save and process photo
            $photoResult = $this->savePhoto($imageService, $attendance);

            // Update attendance record
            $this->updateAttendanceRecord($attendance, $faceResult, $photoResult);

            Log::info("Successfully processed attendance {$this->attendanceId}");

        } catch (Exception $e) {
            Log::error("Failed to process attendance {$this->attendanceId}: " . $e->getMessage());

            // Mark attendance as failed if this is the last attempt
            if ($this->attempts() >= $this->tries) {
                $this->markAttendanceAsFailed($e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Process face verification with Biznet API
     */
    private function processFaceVerification(FaceRecognitionService $faceService): array
    {
        try {
            $result = $faceService->verifyFace($this->faceId, $this->base64Photo);

            return [
                'verified' => $result['verified'] ?? false,
                'similarity' => $result['similarity'] ?? 0,
                'masker' => $result['masker'] ?? false,
                'status' => $result['status'] ?? '',
                'status_message' => $result['status_message'] ?? ''
            ];

        } catch (Exception $e) {
            Log::error("Face verification failed: " . $e->getMessage());

            return [
                'verified' => false,
                'similarity' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Save attendance photo
     */
    private function savePhoto(ImageProcessingService $imageService, Attendance $attendance): array
    {
        try {
            $result = $imageService->saveAttendancePhoto(
                $this->base64Photo,
                $attendance->user_id,
                $attendance->type
            );

            if ($result['success']) {
                return [
                    'success' => true,
                    'path' => $result['path'],
                    'thumbnail_path' => $result['thumbnail_path'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error']
                ];
            }

        } catch (Exception $e) {
            Log::error("Photo save failed: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update attendance record with results
     */
    private function updateAttendanceRecord(Attendance $attendance, array $faceResult, array $photoResult): void
    {
        $updateData = [
            'face_recognition_result' => $faceResult,
            'face_similarity_score' => $faceResult['similarity'] ?? 0,
        ];

        // Set photo path if successfully saved
        if ($photoResult['success']) {
            $updateData['photo_path'] = $photoResult['path'];
        }

        // Determine final status
        $isFaceValid = ($faceResult['verified'] ?? false) &&
            ($faceResult['similarity'] ?? 0) >= config('services.biznet_face.similarity_threshold', 0.75);

        if ($isFaceValid && $attendance->is_valid_location && $photoResult['success']) {
            $updateData['status'] = 'success';
        } else {
            $updateData['status'] = 'failed';

            $reasons = [];
            if (!$isFaceValid) {
                $reasons[] = 'Verifikasi wajah gagal';
            }
            if (!$attendance->is_valid_location) {
                $reasons[] = 'Lokasi di luar jangkauan';
            }
            if (!$photoResult['success']) {
                $reasons[] = 'Gagal menyimpan foto: ' . ($photoResult['error'] ?? 'Unknown error');
            }

            $updateData['failure_reason'] = implode(', ', $reasons);
        }

        $attendance->update($updateData);
    }

    /**
     * Mark attendance as failed when job fails completely
     */
    private function markAttendanceAsFailed(string $errorMessage): void
    {
        try {
            $attendance = Attendance::find($this->attendanceId);

            if ($attendance) {
                $attendance->update([
                    'status' => 'failed',
                    'failure_reason' => "Background processing failed: {$errorMessage}"
                ]);
            }
        } catch (Exception $e) {
            Log::error("Failed to mark attendance as failed: " . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("ProcessAttendanceJob failed permanently for attendance {$this->attendanceId}: " . $exception->getMessage());

        $this->markAttendanceAsFailed($exception->getMessage());
    }
}
