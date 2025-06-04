<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FaceRecognitionService
{
    private $baseUrl;
    private $accessToken;
    private $defaultFaceGalleryId;

    public function __construct()
    {
        $this->baseUrl = config('services.biznet_face.base_url');
        $this->accessToken = config('services.biznet_face.access_token');
        $this->defaultFaceGalleryId = config('services.biznet_face.default_facegallery_id');
    }

    /**
     * Get API counters (remaining limits)
     */
    public function getCounters(): array
    {
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->withBody(json_encode([
                'trx_id' => $this->generateTransactionId()
            ]), 'application/json')->get($this->baseUrl . '/client/get-counters');

            Log::info('Face API - Get Counters Response', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Validate response structure
                if (!isset($data['status'])) {
                    Log::warning('Face API - Unexpected response structure', ['response' => $data]);

                    // If response doesn't have expected structure, create a normalized response
                    return [
                        'status' => 'success', // Assume success if we got a 200 response
                        'status_message' => 'API accessible but response format differs from documentation',
                        'remaining_limit' => $data // Use whatever data we got
                    ];
                }

                return $data;
            }

            throw new Exception('Failed to get API counters: HTTP ' . $response->status() . ' - ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - Get Counters Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get list of my facegalleries
     */
    public function getMyFaceGalleries(): array
    {
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->get($this->baseUrl . '/facegallery/my-facegalleries');

            Log::info('Face API - My FaceGalleries Response', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'status_message' => 'FaceGalleries retrieved',
                        'facegallery_id' => $data['facegallery_id'] ?? $data
                    ];
                }

                return $data;
            }

            throw new Exception('Failed to get facegalleries: HTTP ' . $response->status() . ' - ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - My FaceGalleries Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new FaceGallery
     */
    public function createFaceGallery(string $faceGalleryId): array
    {
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/facegallery/create-facegallery', [
                'facegallery_id' => $faceGalleryId,
                'trx_id' => $this->generateTransactionId()
            ]);

            Log::info('Face API - Create FaceGallery Response', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Normalize response if needed
                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'status_message' => 'FaceGallery operation completed',
                        'data' => $data
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to create FaceGallery: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Create FaceGallery Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete FaceGallery
     */
    public function deleteFaceGallery(string $faceGalleryId): array
    {
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->withBody(json_encode([
                'facegallery_id' => $faceGalleryId,
                'trx_id' => $this->generateTransactionId()
            ]), 'application/json')->delete($this->baseUrl . '/facegallery/delete-facegallery');

            Log::info('Face API - Delete FaceGallery Response', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'status_message' => 'FaceGallery deleted successfully'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to delete FaceGallery: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Delete FaceGallery Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enroll user face to FaceGallery
     */
    public function enrollFace(string $userId, string $userName, string $base64Image, string $faceGalleryId = null): array
    {
        try {
            $galleryId = $faceGalleryId ?: $this->defaultFaceGalleryId;

            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/facegallery/enroll-face', [
                'user_id' => $userId,
                'user_name' => $userName,
                'facegallery_id' => $galleryId,
                'image' => $base64Image,
                'trx_id' => $this->generateTransactionId()
            ]);

            Log::info('Face API - Enroll Face Response', [
                'user_id' => $userId,
                'status_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 500) // Limit log size
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'status_message' => 'Face enrollment completed',
                        'data' => $data
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to enroll face: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Enroll Face Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List enrolled faces in FaceGallery
     */
    public function listFaces(string $faceGalleryId = null): array
    {
        try {
            $galleryId = $faceGalleryId ?: $this->defaultFaceGalleryId;

            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->withBody(json_encode([
                'facegallery_id' => $galleryId,
                'trx_id' => $this->generateTransactionId()
            ]), 'application/json')->get($this->baseUrl . '/facegallery/list-faces');

            Log::info('Face API - List Faces Response', [
                'status_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 500)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'faces' => $data['faces'] ?? $data, // Use 'faces' key or entire response
                        'status_message' => 'Faces list retrieved'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to list faces: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - List Faces Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify face (1:1 authentication)
     */
    public function verifyFace(string $userId, string $base64Image, string $faceGalleryId = null): array
    {
        try {
            $galleryId = $faceGalleryId ?: $this->defaultFaceGalleryId;

            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/facegallery/verify-face', [
                'user_id' => $userId,
                'facegallery_id' => $galleryId,
                'image' => $base64Image,
                'trx_id' => $this->generateTransactionId()
            ]);

            Log::info('Face API - Verify Face Response', [
                'user_id' => $userId,
                'status_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 500)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    // For verify-face, we need to check if we have the expected fields
                    return [
                        'status' => 'success',
                        'verified' => $data['verified'] ?? false,
                        'similarity' => $data['similarity'] ?? 0,
                        'masker' => $data['masker'] ?? false,
                        'user_name' => $data['user_name'] ?? '',
                        'status_message' => 'Face verification completed'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to verify face: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Verify Face Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Identify face (1:N authentication)
     */
    public function identifyFace(string $base64Image, string $faceGalleryId = null): array
    {
        try {
            $galleryId = $faceGalleryId ?: $this->defaultFaceGalleryId;

            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/facegallery/identify-face', [
                'facegallery_id' => $galleryId,
                'image' => $base64Image,
                'trx_id' => $this->generateTransactionId()
            ]);

            Log::info('Face API - Identify Face Response', [
                'status_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 500)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'confidence_level' => $data['confidence_level'] ?? 0,
                        'mask' => $data['mask'] ?? false,
                        'user_id' => $data['user_id'] ?? '',
                        'user_name' => $data['user_name'] ?? '',
                        'status_message' => 'Face identification completed'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to identify face: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Identify Face Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete enrolled face
     */
    public function deleteFace(string $userId, string $faceGalleryId = null): array
    {
        try {
            $galleryId = $faceGalleryId ?: $this->defaultFaceGalleryId;

            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->withBody(json_encode([
                'user_id' => $userId,
                'facegallery_id' => $galleryId,
                'trx_id' => $this->generateTransactionId()
            ]), 'application/json')->delete($this->baseUrl . '/facegallery/delete-face');

            Log::info('Face API - Delete Face Response', [
                'user_id' => $userId,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'status_message' => 'Face deleted successfully'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to delete face: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Delete Face Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Compare two images
     */
    public function compareImages(string $sourceImage, string $targetImage): array
    {
        try {
            $response = Http::withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/compare-images', [
                'source_image' => $sourceImage,
                'target_image' => $targetImage,
                'trx_id' => $this->generateTransactionId()
            ]);

            Log::info('Face API - Compare Images Response', [
                'status_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 500)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['status'])) {
                    return [
                        'status' => 'success',
                        'similarity' => $data['similarity'] ?? 0,
                        'verified' => $data['verified'] ?? false,
                        'masker' => $data['masker'] ?? false,
                        'status_message' => 'Image comparison completed'
                    ];
                }

                return $data;
            }

            $errorData = $response->json();
            $errorMessage = $errorData['status_message'] ?? 'HTTP ' . $response->status() . ' - ' . $response->body();
            throw new Exception('Failed to compare images: ' . $errorMessage);
        } catch (Exception $e) {
            Log::error('Face API - Compare Images Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique transaction ID for logging
     */
    private function generateTransactionId(): string
    {
        return 'jakakuasa_' . time() . '_' . random_int(1000, 9999);
    }

    /**
     * Validate base64 image
     */
    public function validateBase64Image(string $base64String): bool
    {
        // Remove data URL prefix if exists
        $base64String = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64String);

        // Check if valid base64
        if (!base64_decode($base64String, true)) {
            return false;
        }

        // Check if it's a valid image
        $imageData = base64_decode($base64String);
        $imageInfo = @getimagesizefromstring($imageData);

        return $imageInfo !== false && in_array($imageInfo['mime'], ['image/jpeg', 'image/png']);
    }

    /**
     * Convert image to base64
     */
    public function imageToBase64(string $imagePath): string
    {
        if (!file_exists($imagePath)) {
            throw new Exception('Image file not found');
        }

        $imageData = file_get_contents($imagePath);
        $imageInfo = getimagesize($imagePath);

        if (!$imageInfo) {
            throw new Exception('Invalid image file');
        }

        return base64_encode($imageData);
    }

    /**
     * Get API error message in Indonesian
     */
    public function getErrorMessage(int $statusCode): string
    {
        $errorMessages = [
            400 => 'Format permintaan tidak valid',
            401 => 'Token akses tidak dikenali',
            403 => 'Akses ditolak',
            411 => 'Wajah tidak dapat diverifikasi atau tidak terdaftar',
            412 => 'Wajah tidak terdeteksi',
            413 => 'Wajah terlalu kecil',
            415 => 'ID pengguna tidak ditemukan',
            416 => 'Gallery wajah tidak ditemukan',
            451 => 'Gambar kosong',
            452 => 'ID pengguna kosong',
            453 => 'Nama pengguna kosong',
            454 => 'ID gallery wajah kosong',
            455 => 'Gambar target kosong',
            456 => 'Gambar sumber kosong',
            490 => 'Tidak dapat decode gambar base64',
            491 => 'Tipe gambar tidak dikenali',
            492 => 'Tidak dapat decode gambar target base64',
            493 => 'Error pada gambar',
            494 => 'Tidak dapat decode gambar sumber base64',
            495 => 'Tipe gambar sumber tidak dikenali',
            500 => 'Kesalahan server',
        ];

        return $errorMessages[$statusCode] ?? 'Kesalahan tidak diketahui';
    }

    /**
     * Test API connection and create default FaceGallery if needed
     */
    public function testAndSetupAPI(): array
    {
        try {
            // Test API connection
            $counters = $this->getCounters();

            // Check if default FaceGallery exists
            $galleries = $this->getMyFaceGalleries();

            $defaultExists = false;
            if (isset($galleries['facegallery_id']) && is_array($galleries['facegallery_id'])) {
                $defaultExists = in_array($this->defaultFaceGalleryId, $galleries['facegallery_id']);
            }

            // Create default FaceGallery if it doesn't exist
            if (!$defaultExists) {
                $createResult = $this->createFaceGallery($this->defaultFaceGalleryId);

                return [
                    'success' => true,
                    'message' => 'API connected and default FaceGallery created',
                    'counters' => $counters,
                    'gallery_created' => $createResult
                ];
            }

            return [
                'success' => true,
                'message' => 'API connected and default FaceGallery exists',
                'counters' => $counters,
                'galleries' => $galleries
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}
