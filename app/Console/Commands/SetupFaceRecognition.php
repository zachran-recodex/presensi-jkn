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

            // Handle different response formats
            if (isset($result['status']) && $result['status'] === 'success') {
                $this->line('✅ API connection successful!');
                $this->newLine();
                $this->line('API Quota Information:');

                if (isset($result['remaining_limit'])) {
                    $this->table(
                        ['Metric', 'Remaining'],
                        [
                            ['API Hits', number_format($result['remaining_limit']['n_api_hits'] ?? 0)],
                            ['Face Storage', number_format($result['remaining_limit']['n_face'] ?? 0)],
                            ['Face Galleries', number_format($result['remaining_limit']['n_facegallery'] ?? 0)],
                        ]
                    );
                } else {
                    $this->line('Quota information not available in response');
                }

                return true;
            } elseif (isset($result['status']) && $result['status'] !== 'success') {
                $this->error('❌ API test failed: ' . ($result['status_message'] ?? 'Unknown error'));
                $this->line('Response: ' . json_encode($result, JSON_PRETTY_PRINT));
                return false;
            } else {
                // Response doesn't have expected format, but if we got here without exception, API is responding
                $this->line('⚠️  API is responding but with unexpected format');
                $this->line('Raw response: ' . json_encode($result, JSON_PRETTY_PRINT));

                if ($this->confirm('Continue despite unexpected response format?', false)) {
                    return true;
                }
                return false;
            }

        } catch (Exception $e) {
            $this->error('❌ API connection failed: ' . $e->getMessage());
            $this->newLine();

            // Provide more specific troubleshooting based on error
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Access token not authorized') || str_contains($errorMessage, '401')) {
                $this->line('🔍 Troubleshooting:');
                $this->line('- Check if your BIZNET_FACE_ACCESS_TOKEN is correct');
                $this->line('- Verify token in Biznet Portal: https://portal.biznetgio.com');
                $this->line('- Ensure the token hasn\'t expired');
            } elseif (str_contains($errorMessage, 'Undefined array key') || str_contains($errorMessage, 'status')) {
                $this->line('🔍 Analysis:');
                $this->line('- API is responding but response format differs from documentation');
                $this->line('- This might be due to API version differences');
                $this->line('- Try checking the raw API response in logs');
            } elseif (str_contains($errorMessage, 'Connection') || str_contains($errorMessage, 'timeout')) {
                $this->line('🔍 Troubleshooting:');
                $this->line('- Check internet connectivity');
                $this->line('- Verify firewall settings');
                $this->line('- Try again in a few minutes');
            } else {
                $this->line('🔍 General troubleshooting:');
                $this->line('- Check .env configuration');
                $this->line('- Verify API endpoint URL');
                $this->line('- Review Laravel logs for detailed error info');
            }

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

            if (isset($result['status']) && $result['status'] === 'success') {
                $this->line('✅ Face gallery created successfully!');
                return true;
            } elseif (isset($result['status_message'])) {
                // Check if gallery already exists
                if (str_contains($result['status_message'], 'already exists') ||
                    str_contains($result['status_message'], 'sudah ada')) {
                    $this->line('✅ Face gallery already exists');
                    return true;
                }

                $this->error('❌ Failed to create face gallery: ' . $result['status_message']);
                return false;
            } else {
                // Handle unexpected response format
                $this->line('⚠️  Gallery creation response: ' . json_encode($result, JSON_PRETTY_PRINT));

                if ($this->confirm('Assume gallery creation was successful?', true)) {
                    $this->line('✅ Assuming face gallery was created/exists');
                    return true;
                }
                return false;
            }

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();

            // Check if it's a "gallery already exists" error
            if (str_contains($errorMessage, 'already exists') ||
                str_contains($errorMessage, 'sudah ada') ||
                str_contains($errorMessage, '409')) {
                $this->line('✅ Face gallery already exists');
                return true;
            }

            $this->error('❌ Gallery creation failed: ' . $errorMessage);

            // Provide specific guidance
            if (str_contains($errorMessage, '401')) {
                $this->line('💡 Tip: Check your API token permissions for gallery creation');
            } elseif (str_contains($errorMessage, '416')) {
                $this->line('💡 Tip: Gallery ID format might be invalid');
            }

            return false;
        }
    }
}
