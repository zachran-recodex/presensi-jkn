<?php

namespace App\Jobs;

use App\Services\ImageProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class CleanupOldFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $daysToKeep;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The maximum number of seconds the job should run.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(int $daysToKeep = 90)
    {
        $this->daysToKeep = $daysToKeep;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageProcessingService $imageService): void
    {
        try {
            Log::info("Starting cleanup of files older than {$this->daysToKeep} days");

            $result = $imageService->cleanupOldPhotos($this->daysToKeep);

            if ($result['success']) {
                Log::info("Cleanup completed successfully", [
                    'deleted_files' => $result['deleted_files'],
                    'errors' => $result['errors'],
                    'cutoff_date' => $result['cutoff_date']
                ]);

                // If there were errors, log them but don't fail the job
                if ($result['errors'] > 0) {
                    Log::warning("Some files could not be deleted during cleanup", [
                        'error_count' => $result['errors']
                    ]);
                }
            } else {
                Log::error("Cleanup failed: " . $result['error']);
                throw new Exception("Cleanup failed: " . $result['error']);
            }

        } catch (Exception $e) {
            Log::error("CleanupOldFilesJob failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("CleanupOldFilesJob failed permanently: " . $exception->getMessage());
    }
}
