<?php

namespace App\Console;

use App\Jobs\CleanupOldFilesJob;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CleanupOldPhotos::class,
        Commands\SetupFaceRecognition::class,
        Commands\GenerateAttendanceReport::class,
        Commands\SyncAttendanceData::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily cleanup of old attendance photos (runs at 2 AM)
        $schedule->job(new CleanupOldFilesJob(90))
            ->daily()
            ->at('02:00')
            ->name('cleanup-old-photos')
            ->description('Clean up attendance photos older than 90 days');

        // Weekly attendance data sync (runs every Sunday at 3 AM)
        $schedule->command('attendance:sync --fix-missing --update-late')
            ->weekly()
            ->sundays()
            ->at('03:00')
            ->name('sync-attendance-data')
            ->description('Sync and fix attendance data inconsistencies');

        // Monthly attendance report generation (runs on 1st day of month at 4 AM)
        $schedule->command('attendance:report monthly --export=csv --output=' . storage_path('app/reports/monthly_$(date +%Y_%m).csv'))
            ->monthly()
            ->at('04:00')
            ->name('generate-monthly-report')
            ->description('Generate monthly attendance report');

        // Daily backup reminder for critical data (runs at 1 AM)
        $schedule->call(function () {
            Log::info('Daily backup reminder: Please ensure attendance data is backed up');
        })
            ->daily()
            ->at('01:00')
            ->name('backup-reminder')
            ->description('Daily backup reminder log');

        // Hourly queue processing (if using database queue)
        $schedule->command('queue:work --stop-when-empty')
            ->hourly()
            ->when(function () {
                return config('queue.default') === 'database';
            })
            ->name('process-queue')
            ->description('Process queued jobs');

        // Daily database optimization (runs at 5 AM)
        $schedule->call(function () {
            // Clean up failed jobs older than 7 days
            DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(7))
                ->delete();

            // Clean up old sessions if using database sessions
            if (config('session.driver') === 'database') {
                DB::table('sessions')
                    ->where('last_activity', '<', now()->subDays(30)->timestamp)
                    ->delete();
            }
        })
            ->daily()
            ->at('05:00')
            ->name('database-cleanup')
            ->description('Clean up old database records');

        // Weekly face recognition API quota check (runs every Monday at 9 AM)
        $schedule->call(function () {
            try {
                $faceService = app(FaceRecognitionService::class);
                $counters = $faceService->getCounters();

                if (isset($counters['remaining_limit'])) {
                    $apiHits = $counters['remaining_limit']['n_api_hits'] ?? 0;
                    $faces = $counters['remaining_limit']['n_face'] ?? 0;

                    Log::info('Weekly Face Recognition API quota check', [
                        'remaining_api_hits' => $apiHits,
                        'remaining_faces' => $faces,
                        'checked_at' => now()
                    ]);

                    // Alert if quota is running low
                    if ($apiHits < 1000) {
                        Log::warning('Face Recognition API quota running low', [
                            'remaining_api_hits' => $apiHits
                        ]);
                    }
                }
            } catch (Exception $e) {
                Log::error('Failed to check Face Recognition API quota: ' . $e->getMessage());
            }
        })
            ->weekly()
            ->mondays()
            ->at('09:00')
            ->name('check-face-api-quota')
            ->description('Check Face Recognition API quota');

        // Daily attendance summary log (runs at 11 PM)
        $schedule->call(function () {
            $today = Carbon::today();
            $totalEmployees = Employee::active()->count();
            $presentToday = Attendance::whereDate('attendance_date', $today)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count();

            Log::info('Daily attendance summary', [
                'date' => $today->format('Y-m-d'),
                'total_employees' => $totalEmployees,
                'present_today' => $presentToday,
                'absent_today' => $totalEmployees - $presentToday,
                'attendance_rate' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 2) . '%' : '0%'
            ]);
        })
            ->daily()
            ->at('23:00')
            ->name('daily-attendance-summary')
            ->description('Log daily attendance summary');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
