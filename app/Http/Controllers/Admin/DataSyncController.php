<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DataSyncController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $issues = $this->detectIssues();

        return view('admin.data-sync.index', compact('issues'));
    }

    public function sync(Request $request)
    {
        $operations = $request->input('operations', []);
        $results = [];

        if (in_array('fix_missing_clockout', $operations)) {
            $results['missing_clockout'] = $this->fixMissingClockOut();
        }

        if (in_array('update_late_status', $operations)) {
            $results['late_status'] = $this->updateLateStatus();
        }

        if (in_array('remove_orphaned', $operations)) {
            $results['orphaned'] = $this->removeOrphanedRecords();
        }

        if (in_array('fix_duplicates', $operations)) {
            $results['duplicates'] = $this->fixDuplicateRecords();
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    private function detectIssues()
    {
        $issues = [];

        // Missing clock out
        $missingClockOut = Attendance::where('type', 'clock_in')
            ->where('status', 'success')
            ->where('attendance_date', '>=', Carbon::now()->subDays(30))
            ->whereDoesntHave('user.attendances', function($q) {
                $q->where('type', 'clock_out')
                    ->whereColumn('attendance_date', 'attendances.attendance_date')
                    ->where('status', 'success');
            })
            ->count();

        if ($missingClockOut > 0) {
            $issues['missing_clockout'] = [
                'count' => $missingClockOut,
                'description' => 'Attendance records with clock in but no clock out',
                'fixable' => true
            ];
        }

        // Orphaned records
        $orphaned = Attendance::whereDoesntHave('user')->count();
        if ($orphaned > 0) {
            $issues['orphaned'] = [
                'count' => $orphaned,
                'description' => 'Attendance records without valid user',
                'fixable' => true
            ];
        }

        // Duplicate records
        $duplicates = Attendance::selectRaw('user_id, type, attendance_date, COUNT(*) as count')
            ->where('status', 'success')
            ->groupBy('user_id', 'type', 'attendance_date')
            ->having('count', '>', 1)
            ->get()
            ->count();

        if ($duplicates > 0) {
            $issues['duplicates'] = [
                'count' => $duplicates,
                'description' => 'Duplicate attendance records for same day',
                'fixable' => true
            ];
        }

        // Incorrect late status
        $incorrectLate = Attendance::whereHas('user.employee', function($q) {
            $q->where('is_flexible_time', false);
        })
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->where('attendance_date', '>=', Carbon::now()->subDays(7))
            ->get()
            ->filter(function($attendance) {
                $employee = $attendance->user->employee;
                $clockInTime = Carbon::parse($attendance->attendance_time);
                $scheduledTime = Carbon::parse($attendance->attendance_date)
                    ->setTimeFromTimeString($employee->work_start_time);

                $shouldBeLate = $clockInTime->greaterThan($scheduledTime);
                return $attendance->is_late !== $shouldBeLate;
            })
            ->count();

        if ($incorrectLate > 0) {
            $issues['incorrect_late'] = [
                'count' => $incorrectLate,
                'description' => 'Incorrect late status calculations',
                'fixable' => true
            ];
        }

        return $issues;
    }

    private function fixMissingClockOut()
    {
        $missingClockOuts = Attendance::where('type', 'clock_in')
            ->where('status', 'success')
            ->where('attendance_date', '>=', Carbon::now()->subDays(30))
            ->whereDoesntHave('user.attendances', function($q) {
                $q->where('type', 'clock_out')
                    ->whereColumn('attendance_date', 'attendances.attendance_date')
                    ->where('status', 'success');
            })
            ->with(['user.employee'])
            ->get();

        $fixed = 0;

        foreach ($missingClockOuts as $clockIn) {
            if (!$clockIn->user->employee) continue;

            $employee = $clockIn->user->employee;
            $clockInTime = Carbon::parse($clockIn->attendance_time);

            // Calculate expected clock out
            $expectedClockOut = $clockInTime->copy()->setTimeFromTimeString(
                $employee->work_end_time
            );

            // Don't create for future dates or today
            if ($expectedClockOut->isFuture() || $expectedClockOut->isToday()) {
                continue;
            }

            try {
                Attendance::create([
                    'user_id' => $clockIn->user_id,
                    'location_id' => $clockIn->location_id,
                    'type' => 'clock_out',
                    'attendance_date' => $clockIn->attendance_date,
                    'attendance_time' => $expectedClockOut,
                    'latitude' => $clockIn->latitude,
                    'longitude' => $clockIn->longitude,
                    'is_valid_location' => $clockIn->is_valid_location,
                    'distance_from_office' => $clockIn->distance_from_office,
                    'status' => 'success',
                    'device_info' => 'System Generated - Data Sync',
                    'ip_address' => request()->ip(),
                    'face_recognition_result' => ['system_generated' => true],
                    'notes' => 'Auto-generated clock out by data sync'
                ]);

                $fixed++;
            } catch (\Exception $e) {
                // Log error but continue
            }
        }

        return $fixed;
    }

    private function updateLateStatus()
    {
        $attendances = Attendance::with('user.employee')
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->where('attendance_date', '>=', Carbon::now()->subDays(7))
            ->get();

        $updated = 0;

        foreach ($attendances as $attendance) {
            if (!$attendance->user->employee ||
                $attendance->user->employee->is_flexible_time) {
                continue;
            }

            $employee = $attendance->user->employee;
            $clockInTime = Carbon::parse($attendance->attendance_time);
            $scheduledTime = Carbon::parse($attendance->attendance_date)
                ->setTimeFromTimeString($employee->work_start_time);

            $isLate = $clockInTime->greaterThan($scheduledTime);
            $lateMinutes = $isLate ? $clockInTime->diffInMinutes($scheduledTime) : 0;

            if ($attendance->is_late !== $isLate ||
                $attendance->late_minutes !== $lateMinutes) {

                $attendance->update([
                    'is_late' => $isLate,
                    'late_minutes' => $lateMinutes
                ]);

                $updated++;
            }
        }

        return $updated;
    }

    private function removeOrphanedRecords()
    {
        return Attendance::whereDoesntHave('user')->delete();
    }

    private function fixDuplicateRecords()
    {
        $duplicates = Attendance::selectRaw('user_id, type, attendance_date')
            ->where('status', 'success')
            ->groupBy('user_id', 'type', 'attendance_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $fixed = 0;

        foreach ($duplicates as $duplicate) {
            $records = Attendance::where('user_id', $duplicate->user_id)
                ->where('type', $duplicate->type)
                ->where('attendance_date', $duplicate->attendance_date)
                ->where('status', 'success')
                ->orderBy('created_at')
                ->get();

            // Keep the first record, delete the rest
            for ($i = 1; $i < $records->count(); $i++) {
                $records[$i]->delete();
                $fixed++;
            }
        }

        return $fixed;
    }
}
