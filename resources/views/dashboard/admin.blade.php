<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Employees -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Karyawan
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $totalEmployees }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Present Today -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Hadir Hari Ini
                                </dt>
                                <dd class="text-lg font-medium text-green-600">
                                    {{ $todayStats['present'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absent Today -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Tidak Hadir
                                </dt>
                                <dd class="text-lg font-medium text-red-600">
                                    {{ $todayStats['absent'] }}
                                </dd>
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
                            <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Terlambat Hari Ini
                                </dt>
                                <dd class="text-lg font-medium text-yellow-600">
                                    {{ $todayStats['late'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Weekly Attendance Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                    Kehadiran 7 Hari Terakhir
                </h4>
                <div class="h-64">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Today's Attendance Status -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-pie-chart mr-2 text-blue-600"></i>
                    Status Kehadiran Hari Ini
                </h4>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="todayStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity and Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendance -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-blue-600"></i>
                        Aktivitas Terbaru
                    </h4>
                    <a href="{{ route('admin.attendance.index') }}"
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat Semua
                    </a>
                </div>

                @if($recentAttendance->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($recentAttendance as $attendance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 {{ $attendance->type === 'in' ? 'bg-green-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center mr-3">
                                        <i class="fas {{ $attendance->type === 'in' ? 'fa-sign-in-alt text-green-600' : 'fa-sign-out-alt text-blue-600' }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $attendance->type === 'in' ? 'Clock In' : 'Clock Out' }} -
                                            {{ $attendance->created_at->format('H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $attendance->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <div>Belum ada aktivitas hari ini</div>
                    </div>
                @endif
            </div>

            <!-- Alerts and Actions -->
            <div class="space-y-6">
                <!-- Face Enrollment Alert -->
                @if($usersNeedingFaceEnrollment->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
                            Perlu Registrasi Wajah
                        </h4>
                        <div class="space-y-2 mb-4">
                            @foreach($usersNeedingFaceEnrollment as $user)
                                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                                    <span class="text-sm text-gray-700">{{ $user->name }}</span>
                                    <a href="{{ route('admin.employees.show', $user->id) }}"
                                       class="text-xs text-yellow-600 hover:text-yellow-800">
                                        Daftar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.employees.index') }}"
                           class="w-full inline-block text-center bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-medium py-2 px-4 rounded text-sm transition">
                            Kelola Semua Karyawan
                        </a>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bolt mr-2 text-blue-600"></i>
                        Aksi Cepat
                    </h4>
                    <div class="space-y-3">
                        <a href="{{ route('admin.employees.create') }}"
                           class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition">
                            <i class="fas fa-user-plus mr-2"></i>
                            Tambah Karyawan
                        </a>
                        <a href="{{ route('admin.attendance.today') }}"
                           class="w-full flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Lihat Presensi Hari Ini
                        </a>
                        <a href="{{ route('admin.reports.attendance') }}"
                           class="w-full flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded transition">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Laporan
                        </a>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                        Ringkasan Bulan Ini
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Presensi</span>
                            <span class="font-bold text-blue-600">{{ $monthlyStats['total_attendance'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rata-rata Harian</span>
                            <span class="font-bold text-green-600">{{ $monthlyStats['avg_daily_attendance'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Terlambat</span>
                            <span class="font-bold text-red-600">{{ $monthlyStats['total_late'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Weekly Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($weeklyData, 'date')) !!},
                datasets: [{
                    label: 'Kehadiran',
                    data: {!! json_encode(array_column($weeklyData, 'attendance')) !!},
                    borderColor: 'rgb(37, 99, 235)',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Today's Status Pie Chart
        const todayCtx = document.getElementById('todayStatusChart').getContext('2d');
        const todayChart = new Chart(todayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Tidak Hadir', 'Terlambat'],
                datasets: [{
                    data: [
                        {{ $todayStats['on_time'] }},
                        {{ $todayStats['absent'] }},
                        {{ $todayStats['late'] }}
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                        'rgb(234, 179, 8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
