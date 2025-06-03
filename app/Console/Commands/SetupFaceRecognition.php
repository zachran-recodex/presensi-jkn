<?php

namespace App\Console\Commands;

use App\Services\FaceRecognitionService;
use Illuminate\Console\Command;
use Exception;

class SetupFaceRecognition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:setup {--test : Test the API connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup and test Biznet Face Recognition API';

    /**
     * Execute the console command.
     */
    public function handle(FaceRecognitionService $faceService): int
    {
        $this->info('Setting up Biznet Face Recognition API...');
        $this->newLine();

        // Check configuration
        if (!$this->checkConfiguration()) {
            return self::FAILURE;
        }

        // Test API connection
        if ($this->option('test') || $this->confirm('Test API connection?', true)) {
            if (!$this->testApiConnection($faceService)) {
                return self::FAILURE;
            }
        }

        // Setup face gallery
        if ($this->confirm('Create default face gallery?', true)) {
            if (!$this->setupFaceGallery($faceService)) {
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('✅ Face Recognition setup completed successfully!');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Enroll employee faces through the admin panel');
        $this->line('2. Test attendance with enrolled users');

        return self::SUCCESS;
    }

    /**
     * Check Face Recognition configuration
     */
    private function checkConfiguration(): bool
    {
        $this->info('Checking configuration...');

        $baseUrl = config('services.biznet_face.base_url');
        $accessToken = config('services.biznet_face.access_token');
        $galleryId = config('services.biznet_face.default_facegallery_id');

        if (!$baseUrl) {
            $this->error('❌ BIZNET_FACE_BASE_URL not configured in .env');
            return false;
        }

        if (!$accessToken) {
            $this->error('❌ BIZNET_FACE_ACCESS_TOKEN not configured in .env');
            $this->line('Please add your Biznet Face API token to .env file');
            return false;
        }

        if (!$galleryId) {
            $this->error('❌ BIZNET_FACE_GALLERY_ID not configured in .env');
            return false;
        }

        $this->line('✅ Base URL: ' . $baseUrl);
        $this->line('✅ Access Token: ' . substr($accessToken, 0, 10) . '...');
        $this->line('✅ Gallery ID: ' . $galleryId);

        return true;
    }

    /**
     * Test API connection
     */
    private function testApiConnection(FaceRecognitionService $faceService): bool
    {
        $this->info('Testing API connection...');

        try {
            $result = $faceService->getCounters();

            if ($result['status'] === 'success') {
                $this->line('✅ API connection successful!');
                $this->newLine();
                $this->line('API Quota Information:');
                $this->table(
                    ['Metric', 'Remaining'],
                    [
                        ['API Hits', number_format($result['remaining_limit']['n_api_hits'] ?? 0)],
                        ['Face Storage', number_format($result['remaining_limit']['n_face'] ?? 0)],
                        ['Face Galleries', number_format($result['remaining_limit']['n_facegallery'] ?? 0)],
                    ]
                );
                return true;
            } else {
                $this->error('❌ API test failed: ' . ($result['status_message'] ?? 'Unknown error'));
                return false;
            }

        } catch (Exception $e) {
            $this->error('❌ API connection failed: ' . $e->getMessage());
            $this->newLine();
            $this->line('Possible issues:');
            $this->line('- Invalid access token');
            $this->line('- Network connectivity problems');
            $this->line('- API service temporarily unavailable');
            return false;
        }
    }

    /**
     * Setup face gallery
     */
    private function setupFaceGallery(FaceRecognitionService $faceService): bool
    {
        $galleryId = config('services.biznet_face.default_facegallery_id');

        $this->info("Creating face gallery: {$galleryId}");

        try {
            $result = $faceService->createFaceGallery($galleryId);

            if ($result['status'] === 'success') {
                $this->line('✅ Face gallery created successfully!');
                return true;
            } else {
                // Gallery might already exist
                if (str_contains($result['status_message'] ?? '', 'already exists')) {
                    $this->line('✅ Face gallery already exists');
                    return true;
                }

                $this->error('❌ Failed to create face gallery: ' . ($result['status_message'] ?? 'Unknown error'));
                return false;
            }

        } catch (Exception $e) {
            $this->error('❌ Gallery creation failed: ' . $e->getMessage());
            return false;
        }
    }
}
