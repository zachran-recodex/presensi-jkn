<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Main reports dashboard
     */
    public function index()
    {
        $this->authorize('admin');

        $currentMonth = Carbon::now();

        // Monthly statistics
        $monthlyStats = [
            'total_working_days' => $this->getWorkingDaysInMonth($currentMonth),
            'total_attendances' => Attendance::whereMonth('attendance_date', $currentMonth->month)
                ->whereYear('attendance_date', $currentMonth->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count(),
            'late_attendances' => Attendance::whereMonth('attendance_date', $currentMonth->month)
                ->whereYear('attendance_date', $currentMonth->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->where('is_late', true)
                ->count(),
            'absent_count' => $this->calculateAbsentCount($currentMonth)
        ];

        // Department attendance summary
        $departmentStats = $this->getDepartmentAttendanceStats($currentMonth);

        // Location attendance summary
        $locationStats = $this->getLocationAttendanceStats($currentMonth);

        return view('reports.index', compact('monthlyStats', 'departmentStats', 'locationStats'));
    }

    /**
     * Monthly attendance report
     */
    public function monthly(Request $request)
    {
        $this->authorize('admin');

        $month = $request->month ? Carbon::parse($request->month) : Carbon::now();
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $query = Employee::with(['user', 'location'])
            ->active();

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        $employees = $query->get();

        $reportData = [];
        foreach ($employees as $employee) {
            $attendances = Attendance::where('user_id', $employee->user_id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'success')
                ->get();

            $summary = $employee->getMonthlyAttendanceSummary($month->year, $month->month);

            $reportData[] = [
                'employee' => $employee,
                'summary' => $summary,
                'attendance_rate' => $summary['total_days'] > 0 ?
                    round(($summary['present_days'] / $this->getWorkingDaysInMonth($month)) * 100, 2) : 0
            ];
        }

        $departments = Employee::distinct()->pluck('department')->filter();
        $locations = Location::active()->get();

        return view('reports.monthly', compact('reportData', 'month', 'departments', 'locations'));
    }

    /**
     * Employee individual report
     */
    public function employee(Request $request, Employee $employee)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $employee->user_id) {
            abort(403);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $attendances = Attendance::where('user_id', $employee->user_id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->get()
            ->groupBy('attendance_date');

        // Calculate statistics
        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->filter(function($dayAttendances) {
                return $dayAttendances->where('type', 'clock_in')->where('status', 'success')->isNotEmpty();
            })->count(),
            'late_days' => $attendances->filter(function($dayAttendances) {
                return $dayAttendances->where('type', 'clock_in')->where('is_late', true)->isNotEmpty();
            })->count(),
            'total_work_hours' => 0
        ];

        // Calculate total work hours
        foreach ($attendances as $dayAttendances) {
            $clockIn = $dayAttendances->where('type', 'clock_in')->where('status', 'success')->first();
            $clockOut = $dayAttendances->where('type', 'clock_out')->where('status', 'success')->first();

            if ($clockIn && $clockOut) {
                $startTime = Carbon::parse($clockIn->attendance_time);
                $endTime = Carbon::parse($clockOut->attendance_time);
                $stats['total_work_hours'] += $startTime->diffInHours($endTime, true);
            }
        }

        return view('reports.employee', compact('employee', 'attendances', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Export monthly report to CSV
     */
    public function exportMonthly(Request $request)
    {
        $this->authorize('admin');

        $month = $request->month ? Carbon::parse($request->month) : Carbon::now();
        $employees = Employee::with(['user', 'location'])->active()->get();

        $filename = 'monthly_attendance_' . $month->format('Y-m') . '.csv';

        return response()->streamDownload(function() use ($employees, $month) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID Karyawan',
                'Nama',
                'Departemen',
                'Lokasi',
                'Total Hari Kerja',
                'Hari Hadir',
                'Hari Terlambat',
                'Total Jam Kerja',
                'Tingkat Kehadiran (%)'
            ]);

            foreach ($employees as $employee) {
                $summary = $employee->getMonthlyAttendanceSummary($month->year, $month->month);
                $workingDays = $this->getWorkingDaysInMonth($month);
                $attendanceRate = $workingDays > 0 ? round(($summary['present_days'] / $workingDays) * 100, 2) : 0;

                fputcsv($file, [
                    $employee->employee_id,
                    $employee->user->name,
                    $employee->department,
                    $employee->location->name,
                    $workingDays,
                    $summary['present_days'],
                    $summary['late_days'],
                    number_format($summary['total_work_hours'], 2),
                    $attendanceRate . '%'
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Calculate working days in a month (excluding weekends)
     */
    private function getWorkingDaysInMonth(Carbon $month): int
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $workingDays = 0;

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    /**
     * Calculate absent count for current month
     */
    private function calculateAbsentCount(Carbon $month): int
    {
        $workingDays = $this->getWorkingDaysInMonth($month);
        $activeEmployees = Employee::active()->count();
        $totalExpectedAttendances = $workingDays * $activeEmployees;

        $actualAttendances = Attendance::whereMonth('attendance_date', $month->month)
            ->whereYear('attendance_date', $month->year)
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->count();

        return max(0, $totalExpectedAttendances - $actualAttendances);
    }

    /**
     * Get department attendance statistics
     */
    private function getDepartmentAttendanceStats(Carbon $month): array
    {
        $departments = Employee::distinct()->pluck('department')->filter();
        $stats = [];

        foreach ($departments as $department) {
            $employeeCount = Employee::where('department', $department)->active()->count();
            $attendanceCount = Attendance::whereHas('user.employee', function($q) use ($department) {
                $q->where('department', $department);
            })
                ->whereMonth('attendance_date', $month->month)
                ->whereYear('attendance_date', $month->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count();

            $stats[] = [
                'department' => $department,
                'employee_count' => $employeeCount,
                'attendance_count' => $attendanceCount,
                'attendance_rate' => $employeeCount > 0 ? round(($attendanceCount / ($employeeCount * $this->getWorkingDaysInMonth($month))) * 100, 2) : 0
            ];
        }

        return $stats;
    }

    /**
     * Get location attendance statistics
     */
    private function getLocationAttendanceStats(Carbon $month): array
    {
        $locations = Location::active()->get();
        $stats = [];

        foreach ($locations as $location) {
            $employeeCount = $location->employees()->where('status', 'active')->count();
            $attendanceCount = Attendance::where('location_id', $location->id)
                ->whereMonth('attendance_date', $month->month)
                ->whereYear('attendance_date', $month->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count();

            $stats[] = [
                'location' => $location,
                'employee_count' => $employeeCount,
                'attendance_count' => $attendanceCount,
                'attendance_rate' => $employeeCount > 0 ? round(($attendanceCount / ($employeeCount * $this->getWorkingDaysInMonth($month))) * 100, 2) : 0
            ];
        }

        return $stats;
    }
}
