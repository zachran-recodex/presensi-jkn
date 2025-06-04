<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PhotoCleanupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // Get photo statistics
        $stats = $this->getPhotoStatistics();

        return view('admin.photo-cleanup.index', compact('stats'));
    }

    public function preview(Request $request)
    {
        $days = $request->input('days', 90);
        $cutoffDate = now()->subDays($days);

        $filesToDelete = $this->getOldPhotoFiles($cutoffDate);

        return response()->json([
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'files_count' => count($filesToDelete),
            'estimated_size' => $this->calculateTotalSize($filesToDelete),
            'files' => array_slice($filesToDelete, 0, 50) // Preview first 50
        ]);
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $days = $request->input('days');
        $cutoffDate = now()->subDays($days);

        $filesToDelete = $this->getOldPhotoFiles($cutoffDate);

        $deleted = 0;
        $errors = 0;
        $totalSize = 0;

        foreach ($filesToDelete as $file) {
            try {
                $size = Storage::disk('public')->size($file);
                if (Storage::disk('public')->delete($file)) {
                    $deleted++;
                    $totalSize += $size;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $errors++;
            }
        }

        // Remove empty directories
        $this->removeEmptyDirectories($cutoffDate);

        return back()->with('success',
            "Cleanup completed! Deleted {$deleted} files ({$this->formatBytes($totalSize)}). Errors: {$errors}"
        );
    }

    private function getPhotoStatistics()
    {
        $attendanceDir = 'attendance';
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'oldest_file' => null,
            'newest_file' => null,
            'by_month' => []
        ];

        if (!Storage::disk('public')->exists($attendanceDir)) {
            return $stats;
        }

        $directories = Storage::disk('public')->directories($attendanceDir);

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);
            foreach ($files as $file) {
                $stats['total_files']++;
                $stats['total_size'] += Storage::disk('public')->size($file);

                // Extract date from directory name
                $dateString = basename($dir);
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $dateString);
                    $monthKey = $date->format('Y-m');

                    if (!isset($stats['by_month'][$monthKey])) {
                        $stats['by_month'][$monthKey] = ['files' => 0, 'size' => 0];
                    }

                    $stats['by_month'][$monthKey]['files']++;
                    $stats['by_month'][$monthKey]['size'] += Storage::disk('public')->size($file);
                } catch (\Exception $e) {
                    // Skip invalid date directories
                }
            }
        }

        return $stats;
    }

    private function getOldPhotoFiles(Carbon $cutoffDate)
    {
        $files = [];
        $attendanceDir = 'attendance';

        if (!Storage::disk('public')->exists($attendanceDir)) {
            return $files;
        }

        $directories = Storage::disk('public')->directories($attendanceDir);

        foreach ($directories as $dir) {
            $dateString = basename($dir);
            try {
                $directoryDate = Carbon::createFromFormat('Y-m-d', $dateString);

                if ($directoryDate->lt($cutoffDate)) {
                    $dirFiles = Storage::disk('public')->files($dir);
                    $files = array_merge($files, $dirFiles);
                }
            } catch (\Exception $e) {
                // Skip invalid date directories
            }
        }

        return $files;
    }

    private function calculateTotalSize(array $files)
    {
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }
        return $this->formatBytes($totalSize);
    }

    private function removeEmptyDirectories(Carbon $cutoffDate)
    {
        $attendanceDir = 'attendance';
        $directories = Storage::disk('public')->directories($attendanceDir);

        foreach ($directories as $dir) {
            $dateString = basename($dir);
            try {
                $directoryDate = Carbon::createFromFormat('Y-m-d', $dateString);

                if ($directoryDate->lt($cutoffDate)) {
                    $files = Storage::disk('public')->files($dir);
                    if (empty($files)) {
                        Storage::disk('public')->deleteDirectory($dir);
                    }
                }
            } catch (\Exception $e) {
                // Skip invalid directories
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
