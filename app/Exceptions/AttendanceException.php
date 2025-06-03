<?php

namespace App\Exceptions;

use Exception;

class AttendanceException extends Exception
{
    protected $errorType;
    protected $additionalData;

    public function __construct(string $message, string $errorType = 'general', array $additionalData = [], Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->errorType = $errorType;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the error type
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }

    /**
     * Get additional error data
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * Convert to array for JSON response
     */
    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->message,
            'error_type' => $this->errorType,
            'data' => $this->additionalData
        ];
    }

    /**
     * Employee not found or inactive
     */
    public static function employeeInactive(): self
    {
        return new self(
            'Akun karyawan tidak aktif atau tidak ditemukan',
            'employee_inactive'
        );
    }

    /**
     * Face not enrolled
     */
    public static function faceNotEnrolled(): self
    {
        return new self(
            'Wajah belum didaftarkan dalam sistem. Hubungi admin untuk enrollment.',
            'face_not_enrolled'
        );
    }

    /**
     * Already clocked in today
     */
    public static function alreadyClockedIn(): self
    {
        return new self(
            'Anda sudah melakukan clock in hari ini',
            'already_clocked_in'
        );
    }

    /**
     * Already clocked out today
     */
    public static function alreadyClockedOut(): self
    {
        return new self(
            'Anda sudah melakukan clock out hari ini',
            'already_clocked_out'
        );
    }

    /**
     * Must clock in first
     */
    public static function mustClockInFirst(): self
    {
        return new self(
            'Anda harus melakukan clock in terlebih dahulu',
            'must_clock_in_first'
        );
    }

    /**
     * Location out of range
     */
    public static function locationOutOfRange(float $distance, float $allowedRadius): self
    {
        return new self(
            "Anda berada di luar area kantor. Jarak: " . number_format($distance, 0) . "m (maksimal: " . number_format($allowedRadius, 0) . "m)",
            'location_out_of_range',
            [
                'distance' => $distance,
                'allowed_radius' => $allowedRadius
            ]
        );
    }

    /**
     * Face verification failed
     */
    public static function faceVerificationFailed(float $similarity = 0): self
    {
        return new self(
            'Verifikasi wajah gagal. Pastikan pencahayaan cukup dan wajah terlihat jelas.',
            'face_verification_failed',
            [
                'similarity_score' => $similarity
            ]
        );
    }

    /**
     * GPS not available
     */
    public static function gpsNotAvailable(): self
    {
        return new self(
            'Lokasi GPS tidak dapat dideteksi. Pastikan GPS aktif dan berikan izin akses lokasi.',
            'gps_not_available'
        );
    }

    /**
     * Camera not available
     */
    public static function cameraNotAvailable(): self
    {
        return new self(
            'Kamera tidak dapat diakses. Pastikan kamera berfungsi dan berikan izin akses kamera.',
            'camera_not_available'
        );
    }

    /**
     * Photo capture failed
     */
    public static function photoCaptureFailed(): self
    {
        return new self(
            'Gagal mengambil foto. Silakan coba lagi.',
            'photo_capture_failed'
        );
    }

    /**
     * Network error
     */
    public static function networkError(): self
    {
        return new self(
            'Koneksi internet bermasalah. Periksa koneksi dan coba lagi.',
            'network_error'
        );
    }

    /**
     * Server error
     */
    public static function serverError(): self
    {
        return new self(
            'Terjadi kesalahan server. Silakan coba lagi atau hubungi admin.',
            'server_error'
        );
    }

    /**
     * Invalid attendance time
     */
    public static function invalidAttendanceTime(string $reason = ''): self
    {
        $message = 'Waktu presensi tidak valid';
        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message, 'invalid_time');
    }

    /**
     * Duplicate attendance
     */
    public static function duplicateAttendance(string $type): self
    {
        $typeText = $type === 'clock_in' ? 'clock in' : 'clock out';

        return new self(
            "Presensi {$typeText} sudah dilakukan hari ini",
            'duplicate_attendance',
            ['type' => $type]
        );
    }

    /**
     * Create from validation errors
     */
    public static function fromValidation(array $errors): self
    {
        $message = 'Data presensi tidak valid';
        if (!empty($errors)) {
            $firstError = array_values($errors)[0];
            $message = is_array($firstError) ? $firstError[0] : $firstError;
        }

        return new self($message, 'validation_error', $errors);
    }
}
