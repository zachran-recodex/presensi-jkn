<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Exception;

class FaceSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $config = $this->checkConfiguration();

        return view('admin.face-setup.index', compact('config'));
    }

    public function testConnection(FaceRecognitionService $faceService)
    {
        try {
            $result = $faceService->getCounters();

            if (isset($result['status']) && $result['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'API connection successful!',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API responded but with unexpected format',
                    'data' => $result
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createGallery(Request $request, FaceRecognitionService $faceService)
    {
        $galleryId = $request->input('gallery_id') ?:
            config('services.biznet_face.default_facegallery_id');

        try {
            $result = $faceService->createFaceGallery($galleryId);

            if (isset($result['status']) && $result['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Face gallery created successfully!',
                    'gallery_id' => $galleryId
                ]);
            } else {
                // Check if gallery already exists
                $message = $result['status_message'] ?? 'Unknown error';
                if (str_contains($message, 'already exists') ||
                    str_contains($message, 'sudah ada')) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Face gallery already exists',
                        'gallery_id' => $galleryId
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create gallery: ' . $message
                ], 422);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();

            // Handle "already exists" error
            if (str_contains($message, 'already exists') ||
                str_contains($message, '409')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Face gallery already exists',
                    'gallery_id' => $galleryId
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gallery creation failed: ' . $message
            ], 500);
        }
    }

    public function validateConfig(Request $request)
    {
        $config = [
            'base_url' => $request->input('base_url'),
            'access_token' => $request->input('access_token'),
            'gallery_id' => $request->input('gallery_id'),
        ];

        $validation = [];
        $errors = [];

        // Validate URL
        if (!filter_var($config['base_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid base URL format';
        } else {
            $validation['base_url'] = 'Valid URL format';
        }

        // Validate token (basic check)
        if (strlen($config['access_token']) < 10) {
            $errors[] = 'Access token seems too short';
        } else {
            $validation['access_token'] = 'Token format looks valid';
        }

        // Validate gallery ID
        if (empty($config['gallery_id'])) {
            $errors[] = 'Gallery ID is required';
        } else {
            $validation['gallery_id'] = 'Gallery ID provided';
        }

        return response()->json([
            'success' => empty($errors),
            'validation' => $validation,
            'errors' => $errors
        ]);
    }

    private function checkConfiguration()
    {
        return [
            'base_url' => [
                'value' => config('services.biznet_face.base_url'),
                'status' => config('services.biznet_face.base_url') ? 'ok' : 'missing'
            ],
            'access_token' => [
                'value' => config('services.biznet_face.access_token') ?
                    substr(config('services.biznet_face.access_token'), 0, 10) . '...' : null,
                'status' => config('services.biznet_face.access_token') ? 'ok' : 'missing'
            ],
            'gallery_id' => [
                'value' => config('services.biznet_face.default_facegallery_id'),
                'status' => config('services.biznet_face.default_facegallery_id') ? 'ok' : 'missing'
            ],
            'similarity_threshold' => [
                'value' => config('services.biznet_face.similarity_threshold'),
                'status' => 'ok'
            ]
        ];
    }
}
