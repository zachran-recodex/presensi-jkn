<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class FaceApiDebugController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $config = [
            'base_url' => config('services.biznet_face.base_url'),
            'access_token' => config('services.biznet_face.access_token') ?
                substr(config('services.biznet_face.access_token'), 0, 10) . '...' : 'Not Set',
            'gallery_id' => config('services.biznet_face.default_facegallery_id'),
            'similarity_threshold' => config('services.biznet_face.similarity_threshold'),
            'timeout' => config('services.biznet_face.timeout')
        ];

        return view('admin.face-debug.index', compact('config'));
    }

    public function testConnection(Request $request)
    {
        $endpoint = $request->input('endpoint', 'counters');
        $results = [];

        try {
            switch ($endpoint) {
                case 'counters':
                    $results = $this->testCountersEndpoint();
                    break;
                case 'galleries':
                    $results = $this->testGalleriesEndpoint();
                    break;
                default:
                    $results = $this->testCountersEndpoint();
            }

            return response()->json([
                'success' => true,
                'results' => $results
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function testCountersEndpoint()
    {
        $baseUrl = config('services.biznet_face.base_url');
        $accessToken = config('services.biznet_face.access_token');
        $trxId = 'debug_' . time() . '_' . random_int(1000, 9999);

        $tests = [];

        // Test 1: GET with query parameters
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->get($baseUrl . '/client/get-counters', [
                'trx_id' => $trxId
            ]);

            $tests['get_with_query'] = [
                'method' => 'GET with query parameters',
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response' => $response->json(),
                'headers' => $response->headers()
            ];
        } catch (Exception $e) {
            $tests['get_with_query'] = [
                'method' => 'GET with query parameters',
                'error' => $e->getMessage()
            ];
        }

        // Test 2: POST with request body
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/client/get-counters', [
                'trx_id' => $trxId
            ]);

            $tests['post_with_body'] = [
                'method' => 'POST with request body',
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response' => $response->json(),
                'headers' => $response->headers()
            ];
        } catch (Exception $e) {
            $tests['post_with_body'] = [
                'method' => 'POST with request body',
                'error' => $e->getMessage()
            ];
        }

        return $tests;
    }

    private function testGalleriesEndpoint()
    {
        $baseUrl = config('services.biznet_face.base_url');
        $accessToken = config('services.biznet_face.access_token');

        try {
            $response = Http::withHeaders([
                'Accesstoken' => $accessToken,
                'Content-Type' => 'application/json',
            ])->get($baseUrl . '/facegallery/my-facegalleries');

            return [
                'galleries' => [
                    'method' => 'GET my-facegalleries',
                    'status_code' => $response->status(),
                    'success' => $response->successful(),
                    'response' => $response->json(),
                    'headers' => $response->headers()
                ]
            ];
        } catch (Exception $e) {
            return [
                'galleries' => [
                    'method' => 'GET my-facegalleries',
                    'error' => $e->getMessage()
                ]
            ];
        }
    }

    public function testFaceOperation(Request $request)
    {
        $request->validate([
            'operation' => 'required|in:enroll,verify,identify',
            'user_id' => 'required_unless:operation,identify',
            'user_name' => 'required_if:operation,enroll',
            'image' => 'required|string'
        ]);

        $faceService = app(FaceRecognitionService::class);

        try {
            $result = match($request->operation) {
                'enroll' => $faceService->enrollFace(
                    $request->user_id,
                    $request->user_name,
                    $request->image
                ),
                'verify' => $faceService->verifyFace(
                    $request->user_id,
                    $request->image
                ),
                'identify' => $faceService->identifyFace($request->image)
            };

            return response()->json([
                'success' => true,
                'operation' => $request->operation,
                'result' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'operation' => $request->operation,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
