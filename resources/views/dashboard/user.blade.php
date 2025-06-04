<!-- resources/views/dashboard/user.blade.php -->
<x-app-layout>
    @section('title', 'Dashboard Karyawan')
    @section('breadcrumb', 'Dashboard')

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg text-white overflow-hidden">
            <div class="px-6 py-8 sm:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Selamat datang, {{ $employee->user->name }}!</h1>
                        <p class="mt-1 text-blue-100">{{ $employee->employee_id }} • {{ $employee->department }}</p>
                        <p class="mt-1 text-blue-100">{{ $workSchedule['location'] }}</p>
                    </div>
                    <div class="hidden sm:block">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Work Schedule Info -->
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                        <div class="text-sm text-blue-100">Jam Masuk</div>
                        <div class="text-lg font-semibold">{{ $workSchedule['start_time'] }}</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                        <div class="text-sm text-blue-100">Jam Pulang</div>
                        <div class="text-lg font-semibold">{{ $workSchedule['end_time'] }}</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                        <div class="text-sm text-blue-100">Tipe Jam Kerja</div>
                        <div class="text-lg font-semibold">{{ $workSchedule['is_flexible'] ? 'Fleksibel' : 'Tetap' }}</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                        <div class="text-sm text-blue-100">Status Hari Ini</div>
                        <div class="text-lg font-semibold">
                            @if($todayClockIn && $todayClockOut)
                                Selesai
                            @elseif($todayClockIn)
                                Masuk
                            @else
                                Belum Masuk
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Clock In/Out Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Presensi Hari Ini</h2>

                <div class="space-y-4">
                    <!-- Clock In Status -->
                    <div class="flex items-center justify-between p-4 rounded-lg {{ $todayClockIn ? ($todayClockIn->is_late ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200') : 'bg-gray-50 border border-gray-200' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayClockIn ? ($todayClockIn->is_late ? 'bg-red-100' : 'bg-green-100') : 'bg-gray-100' }}">
                                <i class="fas fa-sign-in-alt {{ $todayClockIn ? ($todayClockIn->is_late ? 'text-red-600' : 'text-green-600') : 'text-gray-400' }}"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Clock In</p>
                                <p class="text-xs text-gray-500">
                                    @if($todayClockIn)
                                        {{ $todayClockIn->attendance_time->format('H:i:s') }}
                                        @if($todayClockIn->is_late)
                                            <span class="text-red-600">(Terlambat {{ $todayClockIn->late_minutes }} menit)</span>
                                        @endif
                                    @else
                                        Belum melakukan clock in
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($todayClockIn)
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            @else
                                <i class="fas fa-clock text-gray-400 text-xl"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Clock Out Status -->
                    <div class="flex items-center justify-between p-4 rounded-lg {{ $todayClockOut ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayClockOut ? 'bg-green-100' : 'bg-gray-100' }}">
                                <i class="fas fa-sign-out-alt {{ $todayClockOut ? 'text-green-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Clock Out</p>
                                <p class="text-xs text-gray-500">
                                    @if($todayClockOut)
                                        {{ $todayClockOut->attendance_time->format('H:i:s') }}
                                    @else
                                        Belum melakukan clock out
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($todayClockOut)
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            @else
                                <i class="fas fa-clock text-gray-400 text-xl"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 space-y-3">
                        @if($canClockIn)
                            <a href="{{ route('attendance.index') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <i class="fas fa-camera mr-2"></i>
                                Clock In Sekarang
                            </a>
                        @elseif($canClockOut)
                            <a href="{{ route('attendance.index') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                <i class="fas fa-camera mr-2"></i>
                                Clock Out Sekarang
                            </a>
                        @else
                            <div class="w-full inline-flex justify-center items-center px-4 py-3 text-base font-medium rounded-md text-gray-500 bg-gray-100">
                                <i class="fas fa-check-circle mr-2"></i>
                                Presensi Hari Ini Selesai
                            </div>
                        @endif

                        <!-- Quick Links -->
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('attendance.history') }}"
                               class="inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-history mr-2"></i>
                                Riwayat
                            </a>
                            <a href="{{ route('reports.employee', $employee) }}"
                               class="inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Bulan Ini</h2>

                <div class="space-y-4">
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $monthlySummary['present_days'] }}</div>
                            <div class="text-sm text-gray-600">Hari Hadir</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $monthlySummary['late_days'] }}</div>
                            <div class="text-sm text-gray-600">Hari Terlambat</div>
                        </div>
                    </div>

                    <!-- Work Hours -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total Jam Kerja</p>
                                <p class="text-xl font-semibold text-blue-600">{{ number_format($monthlySummary['total_work_hours'], 1) }} jam</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Rata-rata per Hari</p>
                                <p class="text-lg font-medium text-blue-600">{{ number_format($monthlySummary['average_work_hours'] ?? 0, 1) }} jam</p>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Rate -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        @php
                            $workingDays = \App\Helpers\AttendanceHelper::getWorkingDaysInMonth(now());
                            $attendanceRate = \App\Helpers\AttendanceHelper::calculateAttendanceRate($monthlySummary['present_days'], $workingDays);
                        @endphp
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Tingkat Kehadiran</p>
                                <p class="text-xl font-semibold {{ $attendanceRate >= 90 ? 'text-green-600' : ($attendanceRate >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $attendanceRate }}%
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">{{ $monthlySummary['present_days'] }} dari {{ $workingDays }} hari kerja</p>
                                <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="h-2 rounded-full {{ $attendanceRate >= 90 ? 'bg-green-500' : ($attendanceRate >= 75 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                         style="width: {{ $attendanceRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Riwayat Presensi Terakhir</h2>
                    <a href="{{ route('attendance.history') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if($recentAttendances->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAttendances->take(7) as $date => $dayAttendances)
                            @php
                                $clockIn = $dayAttendances->where('type', 'clock_in')->first();
                                $clockOut = $dayAttendances->where('type', 'clock_out')->first();
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if($clockIn && $clockOut)
                                                {{ $clockIn->attendance_time->format('H:i') }} - {{ $clockOut->attendance_time->format('H:i') }}
                                                @php
                                                    $workHours = $clockIn->attendance_time->diffInHours($clockOut->attendance_time, true);
                                                @endphp
                                                <span class="ml-2 text-blue-600">({{ number_format($workHours, 1) }} jam)</span>
                                            @elseif($clockIn)
                                                {{ $clockIn->attendance_time->format('H:i') }} - Belum clock out
                                            @else
                                                Tidak ada data
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($clockIn && $clockIn->is_late)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Terlambat
                                        </span>
                                    @elseif($clockIn)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Tepat Waktu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-minus mr-1"></i>
                                            Tidak Hadir
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada riwayat presensi</p>
                        <p class="text-sm text-gray-400 mt-1">Mulai melakukan presensi untuk melihat riwayat</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Important Notes / Announcements -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan wajah Anda terlihat jelas saat melakukan presensi</li>
                            <li>Aktifkan GPS dan berikan izin lokasi untuk presensi yang akurat</li>
                            <li>Lakukan presensi dalam radius {{ $employee->location->radius }}m dari kantor</li>
                            <li>Hubungi admin jika mengalami masalah dengan sistem presensi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh attendance status every 30 seconds
        let refreshInterval;

        function startAutoRefresh() {
            refreshInterval = setInterval(function() {
                // Only refresh if user is active (has interacted in the last 5 minutes)
                if (Date.now() - window.lastActivity < 300000) {
                    refreshAttendanceStatus();
                }
            }, 30000);
        }

        function refreshAttendanceStatus() {
            // This would be implemented to refresh just the attendance status section
            // For now, we'll just update the current time display
            const timeElements = document.querySelectorAll('[data-time]');
            timeElements.forEach(function(element) {
                const time = new Date().toLocaleTimeString('id-ID');
                element.textContent = time;
            });
        }

        // Track user activity
        window.lastActivity = Date.now();
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(function(name) {
            document.addEventListener(name, function() {
                window.lastActivity = Date.now();
            }, { passive: true });
        });

        // Initialize auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });

        // Handle visibility change (pause refresh when tab is not active)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            } else {
                startAutoRefresh();
            }
        });
    </script>
    @endpush
</x-app-layout>
