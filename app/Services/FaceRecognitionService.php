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
            ])->get($this->baseUrl . '/client/get-counters', [
                'trx_id' => $this->generateTransactionId()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get API counters: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - Get Counters Error: ' . $e->getMessage());
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
            ])->post($this->baseUrl . '/facegallery/create-facegallery', [
                'facegallery_id' => $faceGalleryId,
                'trx_id' => $this->generateTransactionId()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create FaceGallery: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - Create FaceGallery Error: ' . $e->getMessage());
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

            if ($response->successful()) {
                return $response->json();
            }

            $errorData = $response->json();
            throw new Exception('Failed to enroll face: ' . ($errorData['status_message'] ?? $response->body()));
        } catch (Exception $e) {
            Log::error('Face API - Enroll Face Error: ' . $e->getMessage());
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

            if ($response->successful()) {
                return $response->json();
            }

            $errorData = $response->json();
            throw new Exception('Failed to verify face: ' . ($errorData['status_message'] ?? $response->body()));
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

            if ($response->successful()) {
                return $response->json();
            }

            $errorData = $response->json();
            throw new Exception('Failed to identify face: ' . ($errorData['status_message'] ?? $response->body()));
        } catch (Exception $e) {
            Log::error('Face API - Identify Face Error: ' . $e->getMessage());
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

            if ($response->successful()) {
                return $response->json();
            }

            $errorData = $response->json();
            throw new Exception('Failed to compare images: ' . ($errorData['status_message'] ?? $response->body()));
        } catch (Exception $e) {
            Log::error('Face API - Compare Images Error: ' . $e->getMessage());
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
            ])->get($this->baseUrl . '/facegallery/list-faces', [
                'facegallery_id' => $galleryId,
                'trx_id' => $this->generateTransactionId()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to list faces: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - List Faces Error: ' . $e->getMessage());
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
            ])->delete($this->baseUrl . '/facegallery/delete-face', [
                'user_id' => $userId,
                'facegallery_id' => $galleryId,
                'trx_id' => $this->generateTransactionId()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to delete face: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Face API - Delete Face Error: ' . $e->getMessage());
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
}
