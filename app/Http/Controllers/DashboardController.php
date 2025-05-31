<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display user dashboard (Satpam)
     */
    public function index(): View
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Get today's attendance
        $todayAttendance = $user->attendances()
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if user has clocked in today
        $hasClockIn = $todayAttendance->where('type', 'in')->isNotEmpty();
        $hasClockOut = $todayAttendance->where('type', 'out')->isNotEmpty();

        // Get last 7 days attendance for chart
        $recentAttendance = $user->attendances()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(14) // Max 2 records per day (in & out)
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

        // User stats
        $stats = [
            'total_attendance_this_month' => $user->attendances()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'on_time_this_month' => $user->attendances()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'in')
                ->whereTime('created_at', '<=', '08:00:00')
                ->count(),
            'late_this_month' => $user->attendances()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'in')
                ->whereTime('created_at', '>', '08:00:00')
                ->count(),
        ];

        return view('dashboard.user', compact(
            'user',
            'todayAttendance',
            'hasClockIn',
            'hasClockOut',
            'recentAttendance',
            'stats'
        ));
    }
}
