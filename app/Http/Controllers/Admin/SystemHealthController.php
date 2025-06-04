<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Location;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class SystemHealthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(FaceRecognitionService $faceService)
    {
        $healthReport = [
            'timestamp' => now(),
            'status' => 'healthy',
            'checks' => [],
            'issues' => [],
            'recommendations' => []
        ];

        // Database Check
        $healthReport['checks']['database'] = $this->checkDatabase();

        // Face API Check
        $healthReport['checks']['face_api'] = $this->checkFaceAPI($faceService);

        // Storage Check
        $healthReport['checks']['storage'] = $this->checkStorage();

        // Data Integrity Check
        $healthReport['checks']['data_integrity'] = $this->checkDataIntegrity();

        // User Accounts Check
        $healthReport['checks']['user_accounts'] = $this->checkUserAccounts();

        // Determine overall status
        foreach ($healthReport['checks'] as $check) {
            if ($check['status'] === 'error') {
                $healthReport['status'] = 'unhealthy';
                break;
            } elseif ($check['status'] === 'warning' && $healthReport['status'] === 'healthy') {
                $healthReport['status'] = 'warning';
            }
        }

        return view('admin.system-health.index', compact('healthReport'));
    }

    private function checkDatabase()
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = (microtime(true) - $start) * 1000;

            return [
                'status' => 'ok',
                'connection_time' => round($connectionTime, 2) . 'ms',
                'message' => 'Database connection successful'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'message' => 'Database connection failed'
            ];
        }
    }

    private function checkFaceAPI(FaceRecognitionService $faceService)
    {
        try {
            $counters = $faceService->getCounters();

            $apiHits = $counters['remaining_limit']['n_api_hits'] ?? 'unknown';
            $faces = $counters['remaining_limit']['n_face'] ?? 'unknown';

            $status = 'ok';
            $warnings = [];

            if (is_numeric($apiHits) && $apiHits < 1000) {
                $status = 'warning';
                $warnings[] = "Low API quota: {$apiHits} calls remaining";
            }

            if (is_numeric($faces) && $faces < 100) {
                $status = 'warning';
                $warnings[] = "Low face storage: {$faces} faces remaining";
            }

            return [
                'status' => $status,
                'api_hits_remaining' => $apiHits,
                'faces_remaining' => $faces,
                'warnings' => $warnings,
                'message' => 'Face API accessible'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'message' => 'Face API connection failed'
            ];
        }
    }

    private function checkStorage()
    {
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        $status = $usagePercent > 90 ? 'warning' : 'ok';

        return [
            'status' => $status,
            'free_space' => $this->formatBytes($freeSpace),
            'usage_percent' => round($usagePercent, 1),
            'message' => $usagePercent > 90 ? 'High disk usage' : 'Storage OK'
        ];
    }

    private function checkDataIntegrity()
    {
        $issues = [];

        // Check orphaned records
        $orphanedAttendances = Attendance::whereDoesntHave('user')->count();
        if ($orphanedAttendances > 0) {
            $issues[] = "{$orphanedAttendances} orphaned attendance records";
        }

        $usersWithoutEmployee = User::where('role', 'user')
            ->whereDoesntHave('employee')->count();
        if ($usersWithoutEmployee > 0) {
            $issues[] = "{$usersWithoutEmployee} users without employee profiles";
        }

        return [
            'status' => empty($issues) ? 'ok' : 'warning',
            'issues' => $issues,
            'message' => empty($issues) ? 'Data integrity OK' : 'Data issues found'
        ];
    }

    private function checkUserAccounts()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'users_with_face' => User::whereNotNull('face_id')->count(),
            'active_employees' => Employee::where('status', 'active')->count()
        ];

        $status = $stats['admin_users'] === 0 ? 'error' : 'ok';

        return [
            'status' => $status,
            'statistics' => $stats,
            'message' => $status === 'error' ? 'No admin users found' : 'User accounts OK'
        ];
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

    // Fix issues action
    public function fixIssues(Request $request)
    {
        $fixed = [];

        // Fix orphaned records
        if ($request->has('fix_orphaned')) {
            $deleted = Attendance::whereDoesntHave('user')->delete();
            if ($deleted > 0) {
                $fixed[] = "Deleted {$deleted} orphaned attendance records";
            }
        }

        return back()->with('success', 'Fixed issues: ' . implode(', ', $fixed));
    }
}
