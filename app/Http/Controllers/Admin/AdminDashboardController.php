<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Total active employees (excluding admins)
        $totalEmployees = User::regularUser()->active()->count();

        // Today's attendance stats
        $todayStats = [
            'present' => User::regularUser()
                ->active()
                ->whereHas('attendances', function($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->where('type', 'in');
                })
                ->count(),
            'absent' => 0, // Will be calculated
            'late' => Attendance::whereDate('created_at', $today)
                ->where('type', 'in')
                ->whereTime('created_at', '>', '08:00:00')
                ->count(),
            'on_time' => Attendance::whereDate('created_at', $today)
                ->where('type', 'in')
                ->whereTime('created_at', '<=', '08:00:00')
                ->count(),
        ];

        // Calculate absent employees
        $todayStats['absent'] = $totalEmployees - $todayStats['present'];

        // Monthly stats
        $monthlyStats = [
            'total_attendance' => Attendance::whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->count(),
            'avg_daily_attendance' => 0, // Will be calculated
            'total_late' => Attendance::whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->where('type', 'in')
                ->whereTime('created_at', '>', '08:00:00')
                ->count(),
        ];

        // Calculate average daily attendance for this month
        $daysInMonth = Carbon::now()->daysInMonth;
        $currentDay = Carbon::now()->day;
        $workingDays = min($currentDay, $daysInMonth);

        if ($workingDays > 0) {
            $monthlyStats['avg_daily_attendance'] = round($monthlyStats['total_attendance'] / $workingDays, 1);
        }

        // Recent attendance logs (last 10 records)
        $recentAttendance = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Weekly attendance chart data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyData[] = [
                'date' => $date->format('M d'),
                'attendance' => Attendance::whereDate('created_at', $date)
                    ->where('type', 'in')
                    ->count(),
            ];
        }

        // Employees needing face enrollment
        $usersNeedingFaceEnrollment = User::regularUser()
            ->active()
            ->where('is_face_enrolled', false)
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalEmployees',
            'todayStats',
            'monthlyStats',
            'recentAttendance',
            'weeklyData',
            'usersNeedingFaceEnrollment'
        ));
    }
}
