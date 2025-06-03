<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Attendance;
use App\Helpers\AttendanceHelper;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateAttendanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:report
                            {type=monthly : Report type (daily, weekly, monthly)}
                            {--date= : Specific date for the report (Y-m-d format)}
                            {--month= : Specific month for monthly report (Y-m format)}
                            {--department= : Filter by department}
                            {--export= : Export format (csv, json)}
                            {--output= : Output file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate attendance reports for analysis and compliance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');

        match($type) {
            'daily' => $this->generateDailyReport(),
            'weekly' => $this->generateWeeklyReport(),
            'monthly' => $this->generateMonthlyReport(),
            default => $this->error("Invalid report type: {$type}")
        };

        return self::SUCCESS;
    }

    /**
     * Generate daily attendance report
     */
    private function generateDailyReport(): void
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        $this->info("Generating daily attendance report for {$date->format('Y-m-d')}");

        $query = Attendance::with(['user.employee', 'location'])
            ->whereDate('attendance_date', $date)
            ->where('status', 'success');

        if ($department = $this->option('department')) {
            $query->whereHas('user.employee', function($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $attendances = $query->get()->groupBy('user_id');

        $reportData = [];
        $summary = [
            'total_employees' => Employee::active()->count(),
            'present_count' => $attendances->count(),
            'absent_count' => 0,
            'late_count' => 0,
            'on_time_count' => 0
        ];

        foreach ($attendances as $userId => $userAttendances) {
            $clockIn = $userAttendances->where('type', 'clock_in')->first();
            $clockOut = $userAttendances->where('type', 'clock_out')->first();

            if ($clockIn) {
                $employee = $clockIn->user->employee;
                $workHours = 0;

                if ($clockIn && $clockOut) {
                    $workHours = AttendanceHelper::calculateWorkingHours(
                        $clockIn->attendance_time,
                        $clockOut->attendance_time
                    );
                }

                if ($clockIn->is_late) {
                    $summary['late_count']++;
                } else {
                    $summary['on_time_count']++;
                }

                $reportData[] = [
                    'employee_id' => $employee->employee_id,
                    'name' => $clockIn->user->name,
                    'department' => $employee->department,
                    'clock_in' => $clockIn->attendance_time->format('H:i:s'),
                    'clock_out' => $clockOut ? $clockOut->attendance_time->format('H:i:s') : '-',
                    'work_hours' => number_format($workHours, 2),
                    'status' => $clockIn->is_late ? 'Late' : 'On Time',
                    'late_minutes' => $clockIn->late_minutes,
                    'location' => $clockIn->location->name ?? '-'
                ];
            }
        }

        $summary['absent_count'] = $summary['total_employees'] - $summary['present_count'];

        $this->displayDailyReport($date, $summary, $reportData);

        if ($this->option('export')) {
            $this->exportReport('daily', $reportData, $date);
        }
    }

    /**
     * Generate weekly attendance report
     */
    private function generateWeeklyReport(): void
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $this->info("Generating weekly attendance report for week of {$startOfWeek->format('Y-m-d')}");

        $attendances = Attendance::with(['user.employee'])
            ->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->get()
            ->groupBy(['user_id', 'attendance_date']);

        $reportData = [];

        foreach ($attendances as $userId => $userAttendances) {
            $user = $userAttendances->first()->first()->user;
            $employee = $user->employee;

            $weekData = [
                'employee_id' => $employee->employee_id,
                'name' => $user->name,
                'department' => $employee->department,
                'total_days' => $userAttendances->count(),
                'late_days' => 0,
                'on_time_days' => 0,
                'total_hours' => 0
            ];

            foreach ($userAttendances as $date => $dayAttendances) {
                $clockIn = $dayAttendances->first();
                if ($clockIn->is_late) {
                    $weekData['late_days']++;
                } else {
                    $weekData['on_time_days']++;
                }
            }

            $reportData[] = $weekData;
        }

        $this->displayWeeklyReport($startOfWeek, $endOfWeek, $reportData);

        if ($this->option('export')) {
            $this->exportReport('weekly', $reportData, $startOfWeek);
        }
    }

    /**
     * Generate monthly attendance report
     */
    private function generateMonthlyReport(): void
    {
        $month = $this->option('month') ?
            Carbon::createFromFormat('Y-m', $this->option('month')) :
            Carbon::now();

        $this->info("Generating monthly attendance report for {$month->format('F Y')}");

        $employees = Employee::with('user')->active()->get();
        $workingDays = AttendanceHelper::getWorkingDaysInMonth($month);

        $reportData = [];

        foreach ($employees as $employee) {
            if ($department = $this->option('department')) {
                if ($employee->department !== $department) {
                    continue;
                }
            }

            $summary = AttendanceHelper::getMonthlyAttendanceSummary($employee, $month);

            $reportData[] = [
                'employee_id' => $employee->employee_id,
                'name' => $employee->user->name,
                'department' => $employee->department,
                'total_working_days' => $workingDays,
                'present_days' => $summary['present_days'],
                'late_days' => $summary['late_days'],
                'absent_days' => $workingDays - $summary['present_days'],
                'total_work_hours' => number_format($summary['total_work_hours'], 2),
                'average_work_hours' => number_format($summary['average_work_hours'], 2),
                'attendance_rate' => AttendanceHelper::calculateAttendanceRate(
                        $summary['present_days'],
                        $workingDays
                    ) . '%'
            ];
        }

        $this->displayMonthlyReport($month, $reportData);

        if ($this->option('export')) {
            $this->exportReport('monthly', $reportData, $month);
        }
    }

    /**
     * Display daily report
     */
    private function displayDailyReport(Carbon $date, array $summary, array $data): void
    {
        $this->newLine();
        $this->line("📊 Daily Attendance Report - {$date->format('l, d F Y')}");
        $this->newLine();

        // Summary
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Employees', $summary['total_employees']],
                ['Present', $summary['present_count']],
                ['Absent', $summary['absent_count']],
                ['On Time', $summary['on_time_count']],
                ['Late', $summary['late_count']],
            ]
        );

        // Detailed attendance
        if (!empty($data)) {
            $this->newLine();
            $this->line('📋 Detailed Attendance:');
            $this->table(
                ['ID', 'Name', 'Department', 'Clock In', 'Clock Out', 'Hours', 'Status'],
                array_map(function($row) {
                    return [
                        $row['employee_id'],
                        $row['name'],
                        $row['department'],
                        $row['clock_in'],
                        $row['clock_out'],
                        $row['work_hours'],
                        $row['status']
                    ];
                }, $data)
            );
        }
    }

    /**
     * Display weekly report
     */
    private function displayWeeklyReport(Carbon $start, Carbon $end, array $data): void
    {
        $this->newLine();
        $this->line("📊 Weekly Attendance Report - {$start->format('d M')} to {$end->format('d M Y')}");
        $this->newLine();

        if (!empty($data)) {
            $this->table(
                ['ID', 'Name', 'Department', 'Days Present', 'On Time', 'Late'],
                array_map(function($row) {
                    return [
                        $row['employee_id'],
                        $row['name'],
                        $row['department'],
                        $row['total_days'],
                        $row['on_time_days'],
                        $row['late_days']
                    ];
                }, $data)
            );
        }
    }

    /**
     * Display monthly report
     */
    private function displayMonthlyReport(Carbon $month, array $data): void
    {
        $this->newLine();
        $this->line("📊 Monthly Attendance Report - {$month->format('F Y')}");
        $this->newLine();

        if (!empty($data)) {
            $this->table(
                ['ID', 'Name', 'Department', 'Present', 'Late', 'Absent', 'Rate'],
                array_map(function($row) {
                    return [
                        $row['employee_id'],
                        $row['name'],
                        $row['department'],
                        $row['present_days'],
                        $row['late_days'],
                        $row['absent_days'],
                        $row['attendance_rate']
                    ];
                }, $data)
            );
        }
    }

    /**
     * Export report to file
     */
    private function exportReport(string $type, array $data, Carbon $date): void
    {
        $format = $this->option('export');
        $filename = $this->option('output') ?: storage_path("app/reports/{$type}_report_{$date->format('Y_m_d')}.{$format}");

        if ($format === 'csv') {
            $this->exportToCsv($data, $filename);
        } elseif ($format === 'json') {
            $this->exportToJson($data, $filename);
        }

        $this->info("Report exported to: {$filename}");
    }

    /**
     * Export to CSV
     */
    private function exportToCsv(array $data, string $filename): void
    {
        $handle = fopen($filename, 'w');

        if (!empty($data)) {
            // Headers
            fputcsv($handle, array_keys($data[0]));

            // Data
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }

        fclose($handle);
    }

    /**
     * Export to JSON
     */
    private function exportToJson(array $data, string $filename): void
    {
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
