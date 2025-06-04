<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Employees -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalEmployees }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today Present -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Hadir Hari Ini</dt>
                                    <dd class="text-lg font-medium text-gray-900" x-data="realtimeStats()" x-text="stats.today_present">{{ $todayAttendances }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today Absent -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tidak Hadir</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $todayAbsent }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Today -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Terlambat</dt>
                                    <dd class="text-lg font-medium text-gray-900" x-data="realtimeStats()" x-text="stats.today_late">{{ $lateToday }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Latest Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Weekly Attendance Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Presensi 7 Hari Terakhir</h3>
                        <div class="h-64">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Latest Attendances -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Presensi Terbaru</h3>
                            <div class="text-sm text-gray-500" x-data="realtimeStats()" x-text="'Update: ' + stats.updated_at"></div>
                        </div>

                        <div class="space-y-3 max-h-64 overflow-y-auto" x-data="realtimeStats()">
                            @if($recentAttendances->count() > 0)
                                @foreach($recentAttendances as $attendance)
                                    <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">
                                                    {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $attendance->user->name }}
                                            </p>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs {{ $attendance->type === 'clock_in' ? 'text-green-600' : 'text-blue-600' }}">
                                                    {{ $attendance->type === 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                                </span>
                                                @if($attendance->is_late)
                                                    <span class="text-xs text-red-600">• Terlambat</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900">{{ $attendance->attendance_time->format('H:i') }}</p>
                                            <p class="text-xs text-gray-500">{{ $attendance->location->name ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Real-time updates container -->
                            <template x-for="attendance in stats.last_attendances" :key="attendance.user_name">
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg bg-blue-50">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-700" x-text="attendance.user_name.substring(0, 2).toUpperCase()"></span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="attendance.user_name"></p>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs"
                                                  :class="attendance.type === 'clock_in' ? 'text-green-600' : 'text-blue-600'"
                                                  x-text="attendance.type === 'clock_in' ? 'Clock In' : 'Clock Out'"></span>
                                            <span x-show="attendance.is_late" class="text-xs text-red-600">• Terlambat</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900" x-text="attendance.time"></p>
                                        <p class="text-xs text-blue-600">Baru</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.attendance.history') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Semua Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees Not Clocked In -->
            @if($notClockedInUsers->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Karyawan Belum Clock In ({{ $notClockedInUsers->count() }})
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($notClockedInUsers as $user)
                                <div class="flex items-center space-x-3 p-3 border border-red-200 bg-red-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-red-200 flex items-center justify-center">
                                            <span class="text-xs font-medium text-red-700">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $user->employee->position ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($user->employee && !$user->employee->is_flexible_time)
                                            @php
                                                $workStart = \Carbon\Carbon::parse($user->employee->work_start_time);
                                                $now = \Carbon\Carbon::now();
                                                $lateMinutes = $now->greaterThan($workStart) ? $now->diffInMinutes($workStart) : 0;
                                            @endphp
                                            @if($lateMinutes > 0)
                                                <span class="text-xs text-red-600 font-medium">+{{ $lateMinutes }}m</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Admin</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <a href="{{ route('employees.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Karyawan</span>
                        </a>

                        <a href="{{ route('locations.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Lokasi</span>
                        </a>

                        <a href="{{ route('face-enrollment.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Face Enrollment</span>
                        </a>

                        <a href="{{ route('admin.attendance.history') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Riwayat Presensi</span>
                        </a>

                        <a href="{{ route('reports.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H9a2 2 0 01-2-2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Laporan</span>
                        </a>

                        <a href="{{ route('face-api-test.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">API Test</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Weekly Attendance Chart
        const weeklyData = @json($weeklyData);

        const ctx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: weeklyData.map(d => d.date),
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: weeklyData.map(d => d.on_time),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Terlambat',
                        data: weeklyData.map(d => d.late),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Real-time stats component
        function realtimeStats() {
            return {
                stats: {
                    today_present: {{ $todayAttendances }},
                    today_late: {{ $lateToday }},
                    today_failed: 0,
                    last_attendances: [],
                    updated_at: new Date().toLocaleTimeString('id-ID')
                },

                init() {
                    this.updateStats();
                    setInterval(() => {
                        this.updateStats();
                    }, 30000); // Update every 30 seconds
                },

                async updateStats() {
                    try {
                        const response = await fetch('{{ route("attendance.realtime-stats") }}');
                        const data = await response.json();

                        // Update stats
                        this.stats.today_present = data.today_present;
                        this.stats.today_late = data.today_late;
                        this.stats.today_failed = data.today_failed;
                        this.stats.last_attendances = data.last_attendances.slice(0, 3); // Show only latest 3
                        this.stats.updated_at = data.updated_at;

                    } catch (error) {
                        console.error('Failed to update stats:', error);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
