<?php

namespace App\Console\Commands;

use App\Services\ImageProcessingService;
use Illuminate\Console\Command;

class CleanupOldPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:cleanup-photos {--days=90 : Number of days to keep photos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old attendance photos to save storage space';

    /**
     * Execute the console command.
     */
    public function handle(ImageProcessingService $imageService): int
    {
        $days = (int) $this->option('days');

        if ($days < 30) {
            $this->error('Minimum retention period is 30 days for compliance reasons.');
            return self::FAILURE;
        }

        $this->info("Starting cleanup of attendance photos older than {$days} days...");

        $result = $imageService->cleanupOldPhotos($days);

        if ($result['success']) {
            $this->info("Cleanup completed successfully!");
            $this->line("Files deleted: {$result['deleted_files']}");
            $this->line("Errors: {$result['errors']}");
            $this->line("Cutoff date: {$result['cutoff_date']}");

            if ($result['errors'] > 0) {
                $this->warn("Some files could not be deleted. Check the logs for details.");
            }

            return self::SUCCESS;
        } else {
            $this->error("Cleanup failed: {$result['error']}");
            return self::FAILURE;
        }
    }
}
