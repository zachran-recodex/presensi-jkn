<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Location;
use App\Services\FaceRecognitionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class CheckSystemHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health
                            {--detailed : Show detailed health information}
                            {--fix : Attempt to fix minor issues}
                            {--export= : Export health report to file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check system health and identify potential issues';

    /**
     * Execute the console command.
     */
    public function handle(FaceRecognitionService $faceService): int
    {
        $this->info('🏥 Starting System Health Check...');
        $this->newLine();

        $healthReport = [
            'timestamp' => now(),
            'status' => 'healthy',
            'checks' => [],
            'issues' => [],
            'recommendations' => []
        ];

        // Database connectivity
        $this->checkDatabaseConnectivity($healthReport);

        // Face Recognition API
        $this->checkFaceRecognitionAPI($faceService, $healthReport);

        // Storage systems
        $this->checkStorageSystems($healthReport);

        // Data integrity
        $this->checkDataIntegrity($healthReport);

        // System resources
        $this->checkSystemResources($healthReport);

        // Application configuration
        $this->checkApplicationConfig($healthReport);

        // User accounts status
        $this->checkUserAccounts($healthReport);

        // Recent attendance patterns
        $this->checkAttendancePatterns($healthReport);

        // Display summary
        $this->displayHealthSummary($healthReport);

        // Auto-fix issues if requested
        if ($this->option('fix')) {
            $this->attemptAutoFix($healthReport);
        }

        // Export report if requested
        if ($this->option('export')) {
            $this->exportHealthReport($healthReport);
        }

        return $healthReport['status'] === 'healthy' ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Check database connectivity and performance
     */
    private function checkDatabaseConnectivity(array &$report): void
    {
        $this->info('🔍 Checking database connectivity...');

        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = (microtime(true) - $start) * 1000;

            $report['checks']['database'] = [
                'status' => 'ok',
                'connection_time' => round($connectionTime, 2) . 'ms'
            ];

            $this->line('✅ Database connection: OK');

            // Check query performance
            $start = microtime(true);
            $userCount = User::count();
            $queryTime = (microtime(true) - $start) * 1000;

            if ($queryTime > 1000) {
                $report['issues'][] = "Slow database queries detected ({$queryTime}ms)";
                $report['recommendations'][] = 'Consider optimizing database indexes';
            }

        } catch (Exception $e) {
            $report['checks']['database'] = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
            $report['status'] = 'unhealthy';
            $report['issues'][] = 'Database connection failed';
            $this->error('❌ Database connection: FAILED');
        }
    }

    /**
     * Check Face Recognition API status
     */
    private function checkFaceRecognitionAPI(FaceRecognitionService $faceService, array &$report): void
    {
        $this->info('🔍 Checking Face Recognition API...');

        try {
            $counters = $faceService->getCounters();

            $report['checks']['face_api'] = [
                'status' => 'ok',
                'remaining_quota' => $counters['remaining_limit'] ?? null
            ];

            $this->line('✅ Face Recognition API: OK');

            // Check quota levels
            if (isset($counters['remaining_limit'])) {
                $apiHits = $counters['remaining_limit']['n_api_hits'] ?? 0;
                $faces = $counters['remaining_limit']['n_face'] ?? 0;

                if ($apiHits < 1000) {
                    $report['issues'][] = "Low API quota: {$apiHits} calls remaining";
                    $report['recommendations'][] = 'Consider upgrading Face API plan';
                }

                if ($faces < 100) {
                    $report['issues'][] = "Low face storage quota: {$faces} faces remaining";
                }
            }

        } catch (Exception $e) {
            $report['checks']['face_api'] = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
            $report['status'] = 'unhealthy';
            $report['issues'][] = 'Face Recognition API unavailable';
            $this->error('❌ Face Recognition API: FAILED');
        }
    }

    /**
     * Check storage systems
     */
    private function checkStorageSystems(array &$report): void
    {
        $this->info('🔍 Checking storage systems...');

        try {
            // Check if storage directories exist and are writable
            $directories = [
                'attendance' => storage_path('app/public/attendance'),
                'reports' => storage_path('app/reports'),
                'logs' => storage_path('logs')
            ];

            $storageIssues = [];

            foreach ($directories as $name => $path) {
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }

                if (!is_writable($path)) {
                    $storageIssues[] = "{$name} directory not writable: {$path}";
                }
            }

            // Check disk space
            $freeSpace = disk_free_space(storage_path());
            $totalSpace = disk_total_space(storage_path());
            $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

            $report['checks']['storage'] = [
                'status' => empty($storageIssues) ? 'ok' : 'warning',
                'free_space' => $this->formatBytes($freeSpace),
                'usage_percent' => round($usagePercent, 1),
                'issues' => $storageIssues
            ];

            if ($usagePercent > 90) {
                $report['issues'][] = "High disk usage: {$usagePercent}%";
                $report['recommendations'][] = 'Clean up old files or increase storage';
            }

            $this->line('✅ Storage systems: OK');

        } catch (Exception $e) {
            $report['checks']['storage'] = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
            $this->error('❌ Storage systems: FAILED');
        }
    }

    /**
     * Check data integrity
     */
    private function checkDataIntegrity(array &$report): void
    {
        $this->info('🔍 Checking data integrity...');

        $issues = [];

        // Check for orphaned records
        $orphanedAttendances = Attendance::whereDoesntHave('user')->count();
        if ($orphanedAttendances > 0) {
            $issues[] = "{$orphanedAttendances} orphaned attendance records";
        }

        // Check for users without employee profiles
        $usersWithoutEmployee = User::where('role', 'user')
            ->whereDoesntHave('employee')
            ->count();
        if ($usersWithoutEmployee > 0) {
            $issues[] = "{$usersWithoutEmployee} users without employee profiles";
        }

        // Check for employees without locations
        $employeesWithoutLocation = Employee::whereDoesntHave('location')->count();
        if ($employeesWithoutLocation > 0) {
            $issues[] = "{$employeesWithoutLocation} employees without assigned locations";
        }

        // Check for duplicate attendance records
        $duplicateAttendances = DB::table('attendances')
            ->select('user_id', 'type', 'attendance_date')
            ->where('status', 'success')
            ->groupBy('user_id', 'type', 'attendance_date')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicateAttendances > 0) {
            $issues[] = "{$duplicateAttendances} duplicate attendance records";
        }

        $report['checks']['data_integrity'] = [
            'status' => empty($issues) ? 'ok' : 'warning',
            'issues' => $issues
        ];

        if (!empty($issues)) {
            $report['issues'] = array_merge($report['issues'], $issues);
            $report['recommendations'][] = 'Run php artisan attendance:sync to fix data issues';
        }

        $this->line('✅ Data integrity: ' . (empty($issues) ? 'OK' : 'ISSUES FOUND'));
    }

    /**
     * Check system resources
     */
    private function checkSystemResources(array &$report): void
    {
        $this->info('🔍 Checking system resources...');

        $resources = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => $this->parseMemoryLimit(ini_get('memory_limit')),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ];

        $memoryUsagePercent = ($resources['memory_usage'] / $resources['memory_limit']) * 100;

        $report['checks']['system_resources'] = [
            'status' => $memoryUsagePercent > 80 ? 'warning' : 'ok',
            'memory_usage_percent' => round($memoryUsagePercent, 1),
            'php_version' => $resources['php_version'],
            'laravel_version' => $resources['laravel_version']
        ];

        if ($memoryUsagePercent > 80) {
            $report['issues'][] = "High memory usage: {$memoryUsagePercent}%";
            $report['recommendations'][] = 'Consider increasing PHP memory limit';
        }

        $this->line('✅ System resources: OK');
    }

    /**
     * Check application configuration
     */
    private function checkApplicationConfig(array &$report): void
    {
        $this->info('🔍 Checking application configuration...');

        $configIssues = [];

        // Check required environment variables
        $requiredEnvVars = [
            'APP_KEY' => 'Application key not set',
            'DB_DATABASE' => 'Database name not configured',
            'BIZNET_FACE_ACCESS_TOKEN' => 'Face API token not configured',
            'BIZNET_FACE_GALLERY_ID' => 'Face gallery ID not configured'
        ];

        foreach ($requiredEnvVars as $var => $message) {
            if (empty(env($var))) {
                $configIssues[] = $message;
            }
        }

        // Check if APP_DEBUG is false in production
        if (app()->environment('production') && config('app.debug')) {
            $configIssues[] = 'APP_DEBUG should be false in production';
        }

        $report['checks']['configuration'] = [
            'status' => empty($configIssues) ? 'ok' : 'error',
            'issues' => $configIssues
        ];

        if (!empty($configIssues)) {
            $report['issues'] = array_merge($report['issues'], $configIssues);
            $report['status'] = 'unhealthy';
        }

        $this->line('✅ Application configuration: ' . (empty($configIssues) ? 'OK' : 'ISSUES FOUND'));
    }

    /**
     * Check user accounts status
     */
    private function checkUserAccounts(array &$report): void
    {
        $this->info('🔍 Checking user accounts...');

        $userStats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'users_with_face' => User::whereNotNull('face_id')->count(),
            'active_employees' => Employee::where('status', 'active')->count()
        ];

        $report['checks']['user_accounts'] = [
            'status' => 'ok',
            'statistics' => $userStats
        ];

        // Check if there are admin users
        if ($userStats['admin_users'] === 0) {
            $report['issues'][] = 'No admin users found in the system';
            $report['status'] = 'unhealthy';
        }

        // Check face enrollment ratio
        $faceEnrollmentRatio = $userStats['active_employees'] > 0 ?
            ($userStats['users_with_face'] / $userStats['active_employees']) * 100 : 0;

        if ($faceEnrollmentRatio < 50) {
            $report['issues'][] = "Low face enrollment ratio: {$faceEnrollmentRatio}%";
            $report['recommendations'][] = 'Enroll more employee faces for better attendance tracking';
        }

        $this->line('✅ User accounts: OK');
    }

    /**
     * Check attendance patterns
     */
    private function checkAttendancePatterns(array &$report): void
    {
        $this->info('🔍 Checking attendance patterns...');

        $recentStats = [
            'today_attendance' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'success')
                ->where('type', 'clock_in')
                ->count(),
            'this_week_attendance' => Attendance::whereBetween('attendance_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
                ->where('status', 'success')
                ->where('type', 'clock_in')
                ->count(),
            'failed_attempts_today' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'failed')
                ->count()
        ];

        $report['checks']['attendance_patterns'] = [
            'status' => 'ok',
            'statistics' => $recentStats
        ];

        // Check for unusual patterns
        if ($recentStats['failed_attempts_today'] > $recentStats['today_attendance']) {
            $report['issues'][] = 'High number of failed attendance attempts today';
            $report['recommendations'][] = 'Check Face API status and user enrollment quality';
        }

        $this->line('✅ Attendance patterns: OK');
    }

    /**
     * Display health summary
     */
    private function displayHealthSummary(array $report): void
    {
        $this->newLine();
        $this->info('📊 System Health Summary');
        $this->newLine();

        $statusColor = $report['status'] === 'healthy' ? 'info' : 'error';
        $statusIcon = $report['status'] === 'healthy' ? '✅' : '❌';

        $this->line("{$statusIcon} Overall Status: " . strtoupper($report['status']));
        $this->newLine();

        if (!empty($report['issues'])) {
            $this->warn('⚠️  Issues Found:');
            foreach ($report['issues'] as $issue) {
                $this->line("  • {$issue}");
            }
            $this->newLine();
        }

        if (!empty($report['recommendations'])) {
            $this->info('💡 Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
            $this->newLine();
        }

        if ($this->option('detailed')) {
            $this->displayDetailedReport($report);
        }
    }

    /**
     * Display detailed health report
     */
    private function displayDetailedReport(array $report): void
    {
        $this->info('📋 Detailed Health Report:');
        $this->newLine();

        foreach ($report['checks'] as $component => $check) {
            $status = $check['status'] === 'ok' ? '✅' : ($check['status'] === 'warning' ? '⚠️' : '❌');
            $this->line("{$status} " . ucwords(str_replace('_', ' ', $component)) . ": " . strtoupper($check['status']));

            if (isset($check['statistics'])) {
                foreach ($check['statistics'] as $key => $value) {
                    $this->line("    " . ucwords(str_replace('_', ' ', $key)) . ": {$value}");
                }
            }

            if (isset($check['issues']) && !empty($check['issues'])) {
                foreach ($check['issues'] as $issue) {
                    $this->line("    ⚠️  {$issue}");
                }
            }
        }
    }

    /**
     * Attempt to auto-fix minor issues
     */
    private function attemptAutoFix(array $report): void
    {
        $this->newLine();
        $this->info('🔧 Attempting auto-fix for minor issues...');

        $fixed = 0;

        // Fix orphaned attendance records
        if (str_contains(implode(' ', $report['issues']), 'orphaned')) {
            $deleted = Attendance::whereDoesntHave('user')->delete();
            if ($deleted > 0) {
                $this->line("✅ Deleted {$deleted} orphaned attendance records");
                $fixed++;
            }
        }

        // Create missing storage directories
        $directories = [
            storage_path('app/public/attendance'),
            storage_path('app/reports'),
            storage_path('logs')
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $this->line("✅ Created missing directory: {$dir}");
                $fixed++;
            }
        }

        if ($fixed > 0) {
            $this->info("🎉 Fixed {$fixed} issues automatically");
        } else {
            $this->line("ℹ️  No auto-fixable issues found");
        }
    }

    /**
     * Export health report to file
     */
    private function exportHealthReport(array $report): void
    {
        $filename = $this->option('export');
        $format = pathinfo($filename, PATHINFO_EXTENSION);

        if ($format === 'json') {
            file_put_contents($filename, json_encode($report, JSON_PRETTY_PRINT));
        } else {
            // Export as CSV
            $handle = fopen($filename, 'w');
            fputcsv($handle, ['Component', 'Status', 'Details']);

            foreach ($report['checks'] as $component => $check) {
                fputcsv($handle, [
                    ucwords(str_replace('_', ' ', $component)),
                    $check['status'],
                    json_encode($check)
                ]);
            }

            fclose($handle);
        }

        $this->info("📄 Health report exported to: {$filename}");
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $limit): int
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);

        return match($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value
        };
    }
}
