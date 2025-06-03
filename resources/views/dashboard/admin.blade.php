<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                    Dashboard Admin
                </h2>
                <p class="text-sm text-gray-600 mt-1">Overview kehadiran karyawan hari ini</p>
            </div>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Employees -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm text-gray-600">Total Karyawan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today Present -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm text-gray-600">Hadir Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $todayAttendances }}</p>
                            <p class="text-xs text-green-600">
                                {{ $totalEmployees > 0 ? round(($todayAttendances / $totalEmployees) * 100, 1) : 0 }}% dari total
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today Absent -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-red-100 text-red-600 p-3 rounded-lg">
                                <i class="fas fa-user-times text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm text-gray-600">Tidak Hadir</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $todayAbsent }}</p>
                            <p class="text-xs text-red-600">
                                {{ $totalEmployees > 0 ? round(($todayAbsent / $totalEmployees) * 100, 1) : 0 }}% dari total
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Today -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm text-gray-600">Terlambat</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $lateToday }}</p>
                            <p class="text-xs text-yellow-600">
                                {{ $todayAttendances > 0 ? round(($lateToday / $todayAttendances) * 100, 1) : 0 }}% dari hadir
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Weekly Attendance Chart -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-area mr-2 text-blue-600"></i>
                            Tren Kehadiran 7 Hari
                        </h3>
                        <button type="button" class="text-sm text-blue-600 hover:text-blue-800">
                            Lihat Detail
                        </button>
                    </div>
                    <div class="h-64" id="attendance-chart">
                        <canvas id="weeklyChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clock mr-2 text-green-600"></i>
                            Presensi Terbaru
                        </h3>
                        <a href="{{ route('admin.attendance.history') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($recentAttendances as $attendance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-100 text-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-600 p-2 rounded-full">
                                        <i class="fas fa-{{ $attendance->type == 'clock_in' ? 'sign-in-alt' : 'sign-out-alt' }} text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attendance->user->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $attendance->type == 'clock_in' ? 'Masuk' : 'Pulang' }} •
                                            {{ $attendance->location->name ?? 'Unknown' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $attendance->attendance_time->format('H:i') }}
                                    </p>
                                    @if($attendance->is_late)
                                        <p class="text-xs text-red-600">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $attendance->late_minutes }}m terlambat
                                        </p>
                                    @else
                                        <p class="text-xs text-green-600">
                                            <i class="fas fa-check mr-1"></i>
                                            Tepat waktu
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-calendar-times text-3xl mb-2"></i>
                                <p>Belum ada presensi hari ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees Not Clocked In -->
        @if($notClockedInUsers->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-times mr-2 text-red-600"></i>
                            Belum Presensi Hari Ini ({{ $notClockedInUsers->count() }} orang)
                        </h3>
                        <button type="button"
                                class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700"
                                onclick="refreshNotClockedIn()">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Refresh
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($notClockedInUsers as $user)
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-red-100 text-red-600 p-2 rounded-full">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $user->employee->employee_id ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->employee->department ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-red-600 font-medium">Belum Masuk</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2 text-purple-600"></i>
                    Aksi Cepat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('employees.create') }}"
                       class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">Tambah Karyawan</p>
                            <p class="text-sm text-blue-600">Daftar karyawan baru</p>
                        </div>
                    </a>

                    <a href="{{ route('face-enrollment.index') }}"
                       class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition-colors">
                        <div class="bg-green-100 text-green-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-face-smile"></i>
                        </div>
                        <div>
                            <p class="font-medium text-green-900">Face Enrollment</p>
                            <p class="text-sm text-green-600">Daftar wajah karyawan</p>
                        </div>
                    </a>

                    <a href="{{ route('reports.daily') }}"
                       class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors">
                        <div class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <p class="font-medium text-purple-900">Laporan Harian</p>
                            <p class="text-sm text-purple-600">Lihat kehadiran hari ini</p>
                        </div>
                    </a>

                    <a href="{{ route('locations.index') }}"
                       class="flex items-center p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors">
                        <div class="bg-orange-100 text-orange-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="font-medium text-orange-900">Kelola Lokasi</p>
                            <p class="text-sm text-orange-600">Atur lokasi kantor</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Weekly Attendance Chart
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyData = @json($weeklyData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeklyData.map(item => item.date),
                datasets: [
                    {
                        label: 'Hadir',
                        data: weeklyData.map(item => item.present),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Terlambat',
                        data: weeklyData.map(item => item.late),
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Auto refresh dashboard stats every 5 minutes
        setInterval(function() {
            fetch('{{ route("dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    // Update stats if needed
                    console.log('Dashboard stats updated:', data);
                })
                .catch(error => console.error('Error updating stats:', error));
        }, 300000); // 5 minutes

        // Refresh not clocked in users
        function refreshNotClockedIn() {
            showLoading();
            location.reload();
        }
    </script>
    @endpush
</x-app-layout>
