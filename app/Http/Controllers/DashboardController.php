<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display main dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    /**
     * Admin dashboard with statistics
     */
    private function adminDashboard()
    {
        $today = Carbon::today();

        // Get basic statistics
        $totalEmployees = Employee::active()->count();
        $todayAttendances = Attendance::today()
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->count();

        $todayAbsent = $totalEmployees - $todayAttendances;

        // Late employees today
        $lateToday = Attendance::today()
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->where('is_late', true)
            ->count();

        // Recent attendances for display
        $recentAttendances = Attendance::with(['user', 'location'])
            ->today()
            ->where('status', 'success')
            ->orderBy('attendance_time', 'desc')
            ->limit(10)
            ->get();

        // Employees who haven't clocked in today
        $notClockedInUsers = User::whereHas('employee', function($query) {
            $query->where('status', 'active');
        })
            ->whereDoesntHave('attendances', function($query) use ($today) {
                $query->where('attendance_date', $today)
                    ->where('type', 'clock_in')
                    ->where('status', 'success');
            })
            ->with('employee')
            ->get();

        // Weekly attendance chart data
        $weeklyData = $this->getWeeklyAttendanceData();

        return view('dashboard.admin', compact(
            'totalEmployees',
            'todayAttendances',
            'todayAbsent',
            'lateToday',
            'recentAttendances',
            'notClockedInUsers',
            'weeklyData'
        ));
    }

    /**
     * User dashboard for employees
     */
    private function userDashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('profile.edit')
                ->with('error', 'Profil karyawan belum lengkap. Silakan hubungi admin.');
        }

        $today = Carbon::today();

        // Today's attendance status
        $todayClockIn = $user->getTodayClockIn();
        $todayClockOut = $user->getTodayClockOut();

        // Recent attendance history (last 7 days)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [
                $today->copy()->subDays(6),
                $today
            ])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->get()
            ->groupBy('attendance_date');

        // Monthly summary
        $monthlySummary = $employee->getMonthlyAttendanceSummary(
            $today->year,
            $today->month
        );

        // Check if user can perform attendance
        $canClockIn = !$user->hasClockedInToday() && $employee->status === 'active';
        $canClockOut = $user->hasClockedInToday() && !$user->hasClockedOutToday();

        // Work schedule info
        $workSchedule = [
            'start_time' => $employee->work_start_time->format('H:i'),
            'end_time' => $employee->work_end_time->format('H:i'),
            'is_flexible' => $employee->is_flexible_time,
            'location' => $employee->location->name
        ];

        return view('dashboard.user', compact(
            'employee',
            'todayClockIn',
            'todayClockOut',
            'recentAttendances',
            'monthlySummary',
            'canClockIn',
            'canClockOut',
            'workSchedule'
        ));
    }

    /**
     * Get weekly attendance data for chart
     */
    private function getWeeklyAttendanceData()
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $dailyData = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $present = Attendance::whereDate('attendance_date', $date)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count();

            $late = Attendance::whereDate('attendance_date', $date)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->where('is_late', true)
                ->count();

            $dailyData[] = [
                'date' => $date->format('M d'),
                'present' => $present,
                'late' => $late,
                'on_time' => $present - $late
            ];
        }

        return $dailyData;
    }

    /**
     * API endpoint for dashboard statistics (for AJAX updates)
     */
    public function getStats()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();
        $totalEmployees = Employee::active()->count();
        $todayAttendances = Attendance::today()
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->count();

        return response()->json([
            'total_employees' => $totalEmployees,
            'today_present' => $todayAttendances,
            'today_absent' => $totalEmployees - $todayAttendances,
            'updated_at' => now()->format('H:i:s')
        ]);
    }
}
