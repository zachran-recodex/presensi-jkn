<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DebugFaceApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:debug {--endpoint=counters : API endpoint to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Biznet Face API responses';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Debugging Biznet Face API...');
        $this->newLine();

        $baseUrl = config('services.biznet_face.base_url');
        $accessToken = config('services.biznet_face.access_token');

        if (!$baseUrl || !$accessToken) {
            $this->error('❌ API configuration missing. Please check .env file.');
            return self::FAILURE;
        }

        $endpoint = $this->option('endpoint');

        match($endpoint) {
            'counters' => $this->testCountersEndpoint($baseUrl, $accessToken),
            'galleries' => $this->testGalleriesEndpoint($baseUrl, $accessToken),
            default => $this->testCountersEndpoint($baseUrl, $accessToken)
        };

        return self::SUCCESS;
    }

    /**
     * Test the get-counters endpoint with different approaches
     */
    private function testCountersEndpoint(string $baseUrl, string $accessToken): void
    {
        $this->info('Testing /client/get-counters endpoint...');
        $this->newLine();

        $trxId = 'debug_' . time() . '_' . random_int(1000, 9999);

        // Test 1: GET with query parameters (current implementation)
        $this->line('🧪 Test 1: GET with query parameters');
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->get($baseUrl . '/client/get-counters', [
                'trx_id' => $trxId
            ]);

            $this->displayResponse('GET with query params', $response);
        } catch (Exception $e) {
            $this->error('❌ GET with query params failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 2: GET with request body (unusual but documented)
        $this->line('🧪 Test 2: GET with request body');
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->withBody(json_encode([
                'trx_id' => $trxId
            ]), 'application/json')->get($baseUrl . '/client/get-counters');

            $this->displayResponse('GET with body', $response);
        } catch (Exception $e) {
            $this->error('❌ GET with body failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 3: POST with request body
        $this->line('🧪 Test 3: POST with request body');
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/client/get-counters', [
                'trx_id' => $trxId
            ]);

            $this->displayResponse('POST with body', $response);
        } catch (Exception $e) {
            $this->error('❌ POST with body failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 4: Basic connectivity test
        $this->line('🧪 Test 4: Basic API connectivity');
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
            ])->get($baseUrl);

            $this->line("Status: {$response->status()}");
            $this->line("Headers: " . json_encode($response->headers()));
            $this->line("Body: " . $response->body());
        } catch (Exception $e) {
            $this->error('❌ Basic connectivity failed: ' . $e->getMessage());
        }
    }

    /**
     * Test the my-facegalleries endpoint
     */
    private function testGalleriesEndpoint(string $baseUrl, string $accessToken): void
    {
        $this->info('Testing /facegallery/my-facegalleries endpoint...');
        $this->newLine();

        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->get($baseUrl . '/facegallery/my-facegalleries');

            $this->displayResponse('GET my-facegalleries', $response);
        } catch (Exception $e) {
            $this->error('❌ GET my-facegalleries failed: ' . $e->getMessage());
        }
    }

    /**
     * Display API response in a readable format
     */
    private function displayResponse(string $method, $response): void
    {
        $this->line("📊 {$method} Response:");
        $this->line("Status Code: {$response->status()}");

        if ($response->successful()) {
            $this->line("✅ Request successful");
        } else {
            $this->line("❌ Request failed");
        }

        // Display headers
        $this->line("Headers:");
        foreach ($response->headers() as $key => $value) {
            $this->line("  {$key}: " . (is_array($value) ? implode(', ', $value) : $value));
        }

        // Display body
        $body = $response->body();
        $this->line("Response Body:");

        // Try to format as JSON if possible
        $jsonData = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->line(json_encode($jsonData, JSON_PRETTY_PRINT));

            // Analyze structure
            $this->line("Structure Analysis:");
            if (isset($jsonData['status'])) {
                $this->line("  ✅ Has 'status' field: {$jsonData['status']}");
            } else {
                $this->line("  ❌ Missing 'status' field");
            }

            if (isset($jsonData['status_message'])) {
                $this->line("  ✅ Has 'status_message' field: {$jsonData['status_message']}");
            } else {
                $this->line("  ⚠️  Missing 'status_message' field");
            }

            if (isset($jsonData['remaining_limit'])) {
                $this->line("  ✅ Has 'remaining_limit' field");
            } else {
                $this->line("  ⚠️  Missing 'remaining_limit' field");
            }

        } else {
            $this->line($body);
        }
    }
}
