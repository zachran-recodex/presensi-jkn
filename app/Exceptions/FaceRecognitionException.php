<?php

namespace App\Exceptions;

use Exception;

class FaceRecognitionException extends Exception
{
    protected $errorCode;
    protected $apiResponse;

    public function __construct(string $message = "", int $errorCode = 0, array $apiResponse = [], Exception $previous = null)
    {
        parent::__construct($message, $errorCode, $previous);

        $this->errorCode = $errorCode;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Get the API error code
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Get the full API response
     */
    public function getApiResponse(): array
    {
        return $this->apiResponse;
    }

    /**
     * Get user-friendly error message in Indonesian
     */
    public function getUserFriendlyMessage(): string
    {
        return match($this->errorCode) {
            400 => 'Format permintaan tidak valid',
            401 => 'Token akses Face Recognition tidak valid',
            403 => 'Akses ke layanan Face Recognition ditolak',
            411 => 'Wajah tidak dapat diverifikasi atau tidak terdaftar',
            412 => 'Wajah tidak terdeteksi pada foto. Pastikan wajah terlihat jelas.',
            413 => 'Wajah terlalu kecil. Dekatkan kamera ke wajah.',
            415 => 'Pengguna tidak ditemukan dalam sistem Face Recognition',
            416 => 'Gallery wajah tidak ditemukan',
            451 => 'Foto tidak dapat diproses',
            452 => 'ID pengguna tidak valid',
            453 => 'Nama pengguna tidak valid',
            454 => 'ID gallery wajah tidak valid',
            490 => 'Format foto tidak valid. Gunakan JPG atau PNG.',
            491 => 'Tipe foto tidak dikenali',
            500 => 'Terjadi kesalahan pada server Face Recognition',
            default => $this->message ?: 'Terjadi kesalahan pada sistem Face Recognition'
        };
    }

    /**
     * Convert to array for JSON response
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->getUserFriendlyMessage(),
            'error_code' => $this->errorCode,
            'original_message' => $this->message,
            'api_response' => $this->apiResponse
        ];
    }

    /**
     * Create exception from API response
     */
    public static function fromApiResponse(array $response, string $operation = ''): self
    {
        $message = $response['status_message'] ?? 'Unknown error';
        $errorCode = 0;

        // Try to extract error code from status message or HTTP response
        if (isset($response['status']) && $response['status'] !== 'success') {
            // Common error patterns from Biznet Face API
            if (str_contains($message, 'Face not detected')) {
                $errorCode = 412;
            } elseif (str_contains($message, 'Face too small')) {
                $errorCode = 413;
            } elseif (str_contains($message, 'not found')) {
                $errorCode = 415;
            } elseif (str_contains($message, 'invalid') || str_contains($message, 'malformed')) {
                $errorCode = 400;
            } elseif (str_contains($message, 'token') || str_contains($message, 'unauthorized')) {
                $errorCode = 401;
            } else {
                $errorCode = 500;
            }
        }

        $fullMessage = $operation ? "Face Recognition {$operation}: {$message}" : $message;

        return new self($fullMessage, $errorCode, $response);
    }

    /**
     * Create exception for quota exceeded
     */
    public static function quotaExceeded(): self
    {
        return new self(
            'Kuota API Face Recognition telah habis',
            429,
            ['status' => 'error', 'status_message' => 'API quota exceeded']
        );
    }

    /**
     * Create exception for network timeout
     */
    public static function timeout(): self
    {
        return new self(
            'Timeout connecting to Face Recognition service',
            408,
            ['status' => 'error', 'status_message' => 'Request timeout']
        );
    }

    /**
     * Create exception for invalid image
     */
    public static function invalidImage(string $reason = ''): self
    {
        $message = 'Format gambar tidak valid';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message, 490);
    }
}
