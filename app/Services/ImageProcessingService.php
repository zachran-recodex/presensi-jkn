<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ImageProcessingService
{
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
    const THUMBNAIL_WIDTH = 300;
    const THUMBNAIL_HEIGHT = 300;

    /**
     * Validate base64 image string
     *
     * @param string $base64String
     * @return array
     */
    public function validateBase64Image(string $base64String): array
    {
        try {
            // Remove data URL prefix if exists
            $cleanBase64 = $this->cleanBase64String($base64String);

            // Check if valid base64
            $imageData = base64_decode($cleanBase64, true);
            if ($imageData === false) {
                return [
                    'valid' => false,
                    'error' => 'String base64 tidak valid'
                ];
            }

            // Check file size
            $fileSize = strlen($imageData);
            if ($fileSize > self::MAX_FILE_SIZE) {
                return [
                    'valid' => false,
                    'error' => 'Ukuran file terlalu besar. Maksimal ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB'
                ];
            }

            // Check if it's a valid image
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                return [
                    'valid' => false,
                    'error' => 'Data bukan gambar yang valid'
                ];
            }

            // Check MIME type
            if (!in_array($imageInfo['mime'], self::ALLOWED_MIME_TYPES)) {
                return [
                    'valid' => false,
                    'error' => 'Format gambar tidak didukung. Gunakan JPG atau PNG'
                ];
            }

            // Check minimum dimensions
            if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
                return [
                    'valid' => false,
                    'error' => 'Resolusi gambar terlalu kecil. Minimal 100x100 pixel'
                ];
            }

            return [
                'valid' => true,
                'size' => $fileSize,
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime_type' => $imageInfo['mime'],
                'extension' => $this->getExtensionFromMime($imageInfo['mime'])
            ];

        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => 'Gagal memvalidasi gambar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Save attendance photo from base64 string
     *
     * @param string $base64String
     * @param int $userId
     * @param string $type (clock_in or clock_out)
     * @return array
     */
    public function saveAttendancePhoto(string $base64String, int $userId, string $type): array
    {
        try {
            // Validate image
            $validation = $this->validateBase64Image($base64String);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['error']
                ];
            }

            // Clean base64 string
            $cleanBase64 = $this->cleanBase64String($base64String);
            $imageData = base64_decode($cleanBase64);

            // Generate filename
            $extension = $validation['extension'];
            $date = now()->format('Y-m-d');
            $time = now()->format('His');
            $filename = "attendance/{$date}/user_{$userId}_{$type}_{$time}.{$extension}";

            // Save original image
            $saved = Storage::disk('public')->put($filename, $imageData);

            if (!$saved) {
                return [
                    'success' => false,
                    'error' => 'Gagal menyimpan foto'
                ];
            }

            // Create thumbnail
            $thumbnailPath = $this->createThumbnail($filename, $imageData);

            return [
                'success' => true,
                'path' => $filename,
                'thumbnail_path' => $thumbnailPath,
                'size' => $validation['size'],
                'dimensions' => [
                    'width' => $validation['width'],
                    'height' => $validation['height']
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Gagal menyimpan foto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create thumbnail from image data
     *
     * @param string $originalPath
     * @param string $imageData
     * @return string|null
     */
    public function createThumbnail(string $originalPath, string $imageData): ?string
    {
        try {
            // Create image resource from string
            $image = @imagecreatefromstring($imageData);
            if (!$image) {
                return null;
            }

            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calculate thumbnail dimensions maintaining aspect ratio
            $ratio = min(self::THUMBNAIL_WIDTH / $originalWidth, self::THUMBNAIL_HEIGHT / $originalHeight);
            $thumbnailWidth = round($originalWidth * $ratio);
            $thumbnailHeight = round($originalHeight * $ratio);

            // Create thumbnail image
            $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            // Preserve transparency for PNG
            if (imagetypes() & IMG_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefill($thumbnail, 0, 0, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $thumbnail, $image,
                0, 0, 0, 0,
                $thumbnailWidth, $thumbnailHeight,
                $originalWidth, $originalHeight
            );

            // Generate thumbnail filename
            $pathInfo = pathinfo($originalPath);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

            // Save thumbnail
            ob_start();
            if ($pathInfo['extension'] === 'png') {
                imagepng($thumbnail);
            } else {
                imagejpeg($thumbnail, null, 85);
            }
            $thumbnailData = ob_get_clean();

            Storage::disk('public')->put($thumbnailPath, $thumbnailData);

            // Clean up memory
            imagedestroy($image);
            imagedestroy($thumbnail);

            return $thumbnailPath;

        } catch (Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get image URL from storage path
     *
     * @param string|null $path
     * @param bool $thumbnail
     * @return string|null
     */
    public function getImageUrl(?string $path, bool $thumbnail = false): ?string
    {
        if (!$path) {
            return null;
        }

        if ($thumbnail) {
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

            if (Storage::disk('public')->exists($thumbnailPath)) {
                return Storage::disk('public')->url($thumbnailPath);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }

    /**
     * Delete attendance photos
     *
     * @param string $path
     * @return bool
     */
    public function deleteAttendancePhoto(string $path): bool
    {
        try {
            $deleted = false;

            // Delete original image
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted = true;
            }

            // Delete thumbnail
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return $deleted;

        } catch (Exception $e) {
            \Log::error('Failed to delete attendance photo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Compress image if needed
     *
     * @param string $imageData
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string|false
     */
    public function compressImage(string $imageData, int $maxWidth = 1024, int $maxHeight = 1024, int $quality = 85)
    {
        try {
            $image = @imagecreatefromstring($imageData);
            if (!$image) {
                return false;
            }

            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Check if compression is needed
            if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
                return $imageData;
            }

            // Calculate new dimensions
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);

            // Create compressed image
            $compressedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            if (imagetypes() & IMG_PNG) {
                imagealphablending($compressedImage, false);
                imagesavealpha($compressedImage, true);
                $transparent = imagecolorallocatealpha($compressedImage, 255, 255, 255, 127);
                imagefill($compressedImage, 0, 0, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $compressedImage, $image,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            // Output compressed image
            ob_start();
            imagejpeg($compressedImage, null, $quality);
            $compressedData = ob_get_clean();

            // Clean up memory
            imagedestroy($image);
            imagedestroy($compressedImage);

            return $compressedData;

        } catch (Exception $e) {
            \Log::error('Image compression failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean base64 string by removing data URL prefix
     *
     * @param string $base64String
     * @return string
     */
    private function cleanBase64String(string $base64String): string
    {
        return preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64String);
    }

    /**
     * Get file extension from MIME type
     *
     * @param string $mimeType
     * @return string
     */
    private function getExtensionFromMime(string $mimeType): string
    {
        return match($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            default => 'jpg'
        };
    }

    /**
     * Convert image to base64 for API usage
     *
     * @param string $imagePath
     * @return string|false
     */
    public function imageToBase64(string $imagePath)
    {
        try {
            if (!Storage::disk('public')->exists($imagePath)) {
                return false;
            }

            $imageData = Storage::disk('public')->get($imagePath);
            return base64_encode($imageData);

        } catch (Exception $e) {
            \Log::error('Failed to convert image to base64: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up old attendance photos (older than specified days)
     *
     * @param int $daysOld
     * @return array
     */
    public function cleanupOldPhotos(int $daysOld = 90): array
    {
        try {
            $cutoffDate = now()->subDays($daysOld);
            $deleted = 0;
            $errors = 0;

            $attendanceDirectories = Storage::disk('public')->directories('attendance');

            foreach ($attendanceDirectories as $directory) {
                // Extract date from directory name
                $dateString = basename($directory);

                try {
                    $directoryDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);

                    if ($directoryDate->lt($cutoffDate)) {
                        $files = Storage::disk('public')->files($directory);

                        foreach ($files as $file) {
                            if (Storage::disk('public')->delete($file)) {
                                $deleted++;
                            } else {
                                $errors++;
                            }
                        }

                        // Remove empty directory
                        Storage::disk('public')->deleteDirectory($directory);
                    }
                } catch (Exception $e) {
                    // Skip invalid date directories
                    continue;
                }
            }

            return [
                'success' => true,
                'deleted_files' => $deleted,
                'errors' => $errors,
                'cutoff_date' => $cutoffDate->format('Y-m-d')
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
