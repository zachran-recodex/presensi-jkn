<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncAttendanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync
                            {--fix-missing : Fix missing clock_out records}
                            {--update-late : Update late status for existing records}
                            {--date= : Specific date to process (Y-m-d format)}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync and fix attendance data inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting attendance data synchronization...');
        $this->newLine();

        $date = $this->option('date') ? Carbon::parse($this->option('date')) : null;
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $results = [
            'missing_clock_out_fixed' => 0,
            'late_status_updated' => 0,
            'orphaned_records_found' => 0,
            'errors' => 0
        ];

        // Fix missing clock out records
        if ($this->option('fix-missing')) {
            $results['missing_clock_out_fixed'] = $this->fixMissingClockOut($date, $isDryRun);
        }

        // Update late status
        if ($this->option('update-late')) {
            $results['late_status_updated'] = $this->updateLateStatus($date, $isDryRun);
        }

        // Find orphaned records
        $results['orphaned_records_found'] = $this->findOrphanedRecords($date, $isDryRun);

        // Validate data integrity
        $this->validateDataIntegrity($date);

        $this->displayResults($results);

        return self::SUCCESS;
    }

    /**
     * Fix missing clock out records for employees who clocked in
     */
    private function fixMissingClockOut(Carbon $date = null, bool $isDryRun = false): int
    {
        $this->info('🔍 Checking for missing clock out records...');

        $query = Attendance::where('type', 'clock_in')
            ->where('status', 'success')
            ->whereDoesntHave('user.attendances', function($q) {
                $q->where('type', 'clock_out')
                    ->whereColumn('attendance_date', 'attendances.attendance_date')
                    ->where('status', 'success');
            });

        if ($date) {
            $query->whereDate('attendance_date', $date);
        } else {
            // Only check last 30 days to avoid processing too much data
            $query->where('attendance_date', '>=', Carbon::now()->subDays(30));
        }

        $missingClockOuts = $query->with(['user.employee'])->get();

        $this->line("Found {$missingClockOuts->count()} records with missing clock out");

        if ($missingClockOuts->isEmpty()) {
            return 0;
        }

        $fixed = 0;

        foreach ($missingClockOuts as $clockIn) {
            if (!$clockIn->user->employee) {
                continue;
            }

            $employee = $clockIn->user->employee;
            $clockInTime = Carbon::parse($clockIn->attendance_time);

            // Calculate expected clock out time based on work schedule
            $expectedClockOut = $clockInTime->copy()->setTimeFromTimeString(
                $employee->work_end_time
            );

            // If clock in was late, adjust clock out accordingly
            if ($clockIn->is_late) {
                $expectedClockOut->addMinutes($clockIn->late_minutes);
            }

            // Don't create clock out for future dates or today
            if ($expectedClockOut->isFuture() || $expectedClockOut->isToday()) {
                continue;
            }

            if ($isDryRun) {
                $this->line("Would create clock out for {$clockIn->user->name} at {$expectedClockOut->format('Y-m-d H:i:s')}");
            } else {
                try {
                    Attendance::create([
                        'user_id' => $clockIn->user_id,
                        'location_id' => $clockIn->location_id,
                        'type' => 'clock_out',
                        'attendance_date' => $clockIn->attendance_date,
                        'attendance_time' => $expectedClockOut,
                        'photo_path' => null, // System generated
                        'latitude' => $clockIn->latitude,
                        'longitude' => $clockIn->longitude,
                        'is_valid_location' => $clockIn->is_valid_location,
                        'distance_from_office' => $clockIn->distance_from_office,
                        'status' => 'success',
                        'device_info' => 'System Generated',
                        'ip_address' => '127.0.0.1',
                        'face_recognition_result' => ['system_generated' => true],
                        'notes' => 'Clock out generated by system sync'
                    ]);

                    $fixed++;
                    $this->line("✅ Created clock out for {$clockIn->user->name}");
                } catch (\Exception $e) {
                    $this->error("❌ Failed to create clock out for {$clockIn->user->name}: {$e->getMessage()}");
                }
            }
        }

        return $fixed;
    }

    /**
     * Update late status for existing records
     */
    private function updateLateStatus(Carbon $date = null, bool $isDryRun = false): int
    {
        $this->info('🔍 Updating late status for attendance records...');

        $query = Attendance::with('user.employee')
            ->where('type', 'clock_in')
            ->where('status', 'success');

        if ($date) {
            $query->whereDate('attendance_date', $date);
        } else {
            $query->where('attendance_date', '>=', Carbon::now()->subDays(7));
        }

        $attendances = $query->get();

        $this->line("Processing {$attendances->count()} clock in records");

        $updated = 0;

        foreach ($attendances as $attendance) {
            if (!$attendance->user->employee) {
                continue;
            }

            $employee = $attendance->user->employee;

            // Skip flexible time employees
            if ($employee->is_flexible_time) {
                continue;
            }

            $clockInTime = Carbon::parse($attendance->attendance_time);
            $scheduledTime = Carbon::parse($attendance->attendance_date)
                ->setTimeFromTimeString($employee->work_start_time);

            $isLate = $clockInTime->greaterThan($scheduledTime);
            $lateMinutes = $isLate ? $clockInTime->diffInMinutes($scheduledTime) : 0;

            // Check if update is needed
            if ($attendance->is_late !== $isLate || $attendance->late_minutes !== $lateMinutes) {
                if ($isDryRun) {
                    $this->line("Would update late status for {$attendance->user->name}: Late={$isLate}, Minutes={$lateMinutes}");
                } else {
                    $attendance->update([
                        'is_late' => $isLate,
                        'late_minutes' => $lateMinutes
                    ]);
                    $updated++;
                }
            }
        }

        if (!$isDryRun && $updated > 0) {
            $this->line("✅ Updated late status for {$updated} records");
        }

        return $updated;
    }

    /**
     * Find orphaned attendance records
     */
    private function findOrphanedRecords(Carbon $date = null, bool $isDryRun = false): int
    {
        $this->info('🔍 Checking for orphaned attendance records...');

        $query = Attendance::whereDoesntHave('user');

        if ($date) {
            $query->whereDate('attendance_date', $date);
        }

        $orphanedRecords = $query->get();

        if ($orphanedRecords->count() > 0) {
            $this->warn("Found {$orphanedRecords->count()} orphaned attendance records (user deleted)");

            if ($this->confirm('Delete orphaned records?', false)) {
                if (!$isDryRun) {
                    $deleted = $orphanedRecords->count();
                    Attendance::whereDoesntHave('user')->delete();
                    $this->line("✅ Deleted {$deleted} orphaned records");
                }
            }
        }

        return $orphanedRecords->count();
    }

    /**
     * Validate data integrity
     */
    private function validateDataIntegrity(Carbon $date = null): void
    {
        $this->info('🔍 Validating data integrity...');

        $issues = [];

        // Check for duplicate attendance records
        $duplicates = Attendance::selectRaw('user_id, type, attendance_date, COUNT(*) as count')
            ->where('status', 'success')
            ->when($date, function($q) use ($date) {
                $q->whereDate('attendance_date', $date);
            })
            ->groupBy('user_id', 'type', 'attendance_date')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            $issues[] = "Found {$duplicates->count()} duplicate attendance records";
        }

        // Check for attendance without employee profile
        $noEmployee = Attendance::whereDoesntHave('user.employee')
            ->when($date, function($q) use ($date) {
                $q->whereDate('attendance_date', $date);
            })
            ->count();

        if ($noEmployee > 0) {
            $issues[] = "Found {$noEmployee} attendance records for users without employee profile";
        }

        // Check for invalid attendance times
        $invalidTimes = Attendance::whereRaw('TIME(attendance_time) < "06:00:00" OR TIME(attendance_time) > "23:00:00"')
            ->when($date, function($q) use ($date) {
                $q->whereDate('attendance_date', $date);
            })
            ->count();

        if ($invalidTimes > 0) {
            $issues[] = "Found {$invalidTimes} records with unusual attendance times";
        }

        if (empty($issues)) {
            $this->line('✅ Data integrity check passed');
        } else {
            $this->warn('⚠️  Data integrity issues found:');
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }
        }
    }

    /**
     * Display sync results
     */
    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Synchronization Results:');
        $this->newLine();

        $this->table(
            ['Operation', 'Count'],
            [
                ['Missing Clock Out Fixed', $results['missing_clock_out_fixed']],
                ['Late Status Updated', $results['late_status_updated']],
                ['Orphaned Records Found', $results['orphaned_records_found']],
                ['Errors', $results['errors']]
            ]
        );

        $this->newLine();
        $this->info('✅ Attendance data synchronization completed!');
    }
}
