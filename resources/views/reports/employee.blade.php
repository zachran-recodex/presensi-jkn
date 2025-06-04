<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-user-chart mr-2 text-indigo-600"></i>
                    Laporan Karyawan - {{ $employee->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Riwayat presensi dan analisis kehadiran individual</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('employees.show', $employee) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        Profil Karyawan
                    </a>
                @endif
                <button onclick="printReport()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                <a href="{{ auth()->user()->isAdmin() ? route('reports.index') : route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Employee Info Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-{{ $employee->status === 'active' ? 'green' : 'red' }}-100 text-{{ $employee->status === 'active' ? 'green' : 'red' }}-800">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">ID Karyawan:</span>
                                <p class="font-medium text-gray-900">{{ $employee->employee_id }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Jabatan:</span>
                                <p class="font-medium text-gray-900">{{ $employee->position }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Departemen:</span>
                                <p class="font-medium text-gray-900">{{ $employee->department ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Lokasi:</span>
                                <p class="font-medium text-gray-900">{{ $employee->location->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('reports.employee', $employee) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai
                    </label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Akhir
                    </label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           value="{{ $endDate->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="button"
                            onclick="setThisMonth()"
                            class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        Bulan Ini
                    </button>
                    <button type="button"
                            onclick="setLastMonth()"
                            class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        Bulan Lalu
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Hari</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_days'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Hari Hadir</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['present_days'] }}</p>
                        <p class="text-xs text-green-600">
                            {{ $stats['total_days'] > 0 ? round(($stats['present_days'] / $stats['total_days']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Terlambat</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['late_days'] }}</p>
                        <p class="text-xs text-yellow-600">
                            {{ $stats['present_days'] > 0 ? round(($stats['late_days'] / $stats['present_days']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-business-time"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Jam Kerja</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_work_hours'], 1) }}</p>
                        <p class="text-xs text-purple-600">jam</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-timeline mr-2 text-indigo-600"></i>
                    Timeline Presensi
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </p>
            </div>

            @if($attendances->count() > 0)
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($attendances as $date => $dayAttendances)
                            @php
                                $clockIn = $dayAttendances->where('type', 'clock_in')->where('status', 'success')->first();
                                $clockOut = $dayAttendances->where('type', 'clock_out')->where('status', 'success')->first();

                                $workHours = 0;
                                if ($clockIn && $clockOut) {
                                    $start = \Carbon\Carbon::parse($clockIn->attendance_time);
                                    $end = \Carbon\Carbon::parse($clockOut->attendance_time);
                                    $workHours = $start->diffInHours($end, true);
                                }
                            @endphp

                            <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <!-- Date Column -->
                                <div class="flex-shrink-0 text-center w-20">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($date)->format('d') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($date)->format('M') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($date)->isoFormat('ddd') }}
                                    </div>
                                </div>

                                <!-- Status Indicator -->
                                <div class="flex-shrink-0 mt-2">
                                    @if($clockIn && $clockOut)
                                        <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                    @elseif($clockIn)
                                        <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                                    @else
                                        <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                                    @endif
                                </div>

                                <!-- Attendance Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
                                        </h4>

                                        <!-- Status Badge -->
                                        @if($clockIn && $clockOut)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Lengkap
                                            </span>
                                        @elseif($clockIn)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Belum Pulang
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Tidak Hadir
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                        <!-- Clock In -->
                                        <div>
                                            <span class="text-gray-600">Masuk:</span>
                                            @if($clockIn)
                                                <p class="font-medium text-gray-900">{{ $clockIn->attendance_time->format('H:i:s') }}</p>
                                                @if($clockIn->is_late)
                                                    <p class="text-xs text-red-600">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        Terlambat {{ $clockIn->late_minutes }} menit
                                                    </p>
                                                @else
                                                    <p class="text-xs text-green-600">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Tepat waktu
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-gray-400">-</p>
                                            @endif
                                        </div>

                                        <!-- Clock Out -->
                                        <div>
                                            <span class="text-gray-600">Pulang:</span>
                                            <p class="font-medium text-gray-900">
                                                {{ $clockOut ? $clockOut->attendance_time->format('H:i:s') : '-' }}
                                            </p>
                                        </div>

                                        <!-- Work Hours -->
                                        <div>
                                            <span class="text-gray-600">Jam Kerja:</span>
                                            <p class="font-medium text-gray-900">
                                                {{ $workHours > 0 ? number_format($workHours, 1) . 'h' : '-' }}
                                            </p>
                                        </div>

                                        <!-- Location -->
                                        <div>
                                            <span class="text-gray-600">Lokasi:</span>
                                            <p class="font-medium text-gray-900">
                                                {{ $clockIn->location->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    @if($clockIn && $clockIn->notes)
                                        <div class="mt-2 p-2 bg-blue-50 rounded text-sm">
                                            <span class="text-blue-800">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                {{ $clockIn->notes }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Failed Attempts -->
                                    @php
                                        $failedAttempts = $dayAttendances->where('status', 'failed');
                                    @endphp
                                    @if($failedAttempts->count() > 0)
                                        <div class="mt-2 p-2 bg-red-50 rounded text-sm">
                                            <span class="text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $failedAttempts->count() }} percobaan gagal
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data Presensi</h3>
                    <p class="text-gray-600">Tidak ada data presensi untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>

        <!-- Performance Analysis -->
        @if($stats['total_days'] > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                        Analisis Performa
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Attendance Rate -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Tingkat Kehadiran</h4>
                            @php
                                $attendanceRate = $stats['total_days'] > 0 ? round(($stats['present_days'] / $stats['total_days']) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center mb-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-3">
                                    <div class="bg-{{ $attendanceRate >= 90 ? 'green' : ($attendanceRate >= 80 ? 'yellow' : 'red') }}-500 h-4 rounded-full"
                                         style="width: {{ $attendanceRate }}%"></div>
                                </div>
                                <span class="text-lg font-bold text-{{ $attendanceRate >= 90 ? 'green' : ($attendanceRate >= 80 ? 'yellow' : 'red') }}-600">
                                    {{ $attendanceRate }}%
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ $stats['present_days'] }} dari {{ $stats['total_days'] }} hari
                            </p>
                        </div>

                        <!-- Punctuality -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Tingkat Ketepatan Waktu</h4>
                            @php
                                $punctualityRate = $stats['present_days'] > 0 ? round((($stats['present_days'] - $stats['late_days']) / $stats['present_days']) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center mb-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-3">
                                    <div class="bg-{{ $punctualityRate >= 90 ? 'green' : ($punctualityRate >= 80 ? 'yellow' : 'red') }}-500 h-4 rounded-full"
                                         style="width: {{ $punctualityRate }}%"></div>
                                </div>
                                <span class="text-lg font-bold text-{{ $punctualityRate >= 90 ? 'green' : ($punctualityRate >= 80 ? 'yellow' : 'red') }}-600">
                                    {{ $punctualityRate }}%
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ $stats['present_days'] - $stats['late_days'] }} dari {{ $stats['present_days'] }} hari hadir
                            </p>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Ringkasan Performa
                        </h4>
                        <div class="text-sm text-blue-800">
                            @if($attendanceRate >= 90 && $punctualityRate >= 90)
                                <p><i class="fas fa-star text-yellow-500 mr-1"></i> Performa sangat baik! Tingkat kehadiran dan ketepatan waktu excellent.</p>
                            @elseif($attendanceRate >= 80 && $punctualityRate >= 80)
                                <p><i class="fas fa-thumbs-up text-green-500 mr-1"></i> Performa baik. Pertahankan konsistensi kehadiran.</p>
                            @elseif($attendanceRate >= 70)
                                <p><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Performa cukup. Perlu peningkatan kehadiran dan ketepatan waktu.</p>
                            @else
                                <p><i class="fas fa-times-circle text-red-500 mr-1"></i> Performa kurang baik. Diperlukan perbaikan signifikan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Print report
            function printReport() {
                window.print();
            }

            // Quick date setters
            function setThisMonth() {
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
                document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];
            }

            function setLastMonth() {
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);

                document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
                document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];
            }

            // Validate date range
            document.addEventListener('DOMContentLoaded', function() {
                const startDate = document.getElementById('start_date');
                const endDate = document.getElementById('end_date');

                function validateDates() {
                    if (startDate.value && endDate.value) {
                        if (new Date(startDate.value) > new Date(endDate.value)) {
                            endDate.setCustomValidity('Tanggal akhir harus setelah tanggal mulai');
                        } else {
                            endDate.setCustomValidity('');
                        }
                    }
                }

                startDate.addEventListener('change', validateDates);
                endDate.addEventListener('change', validateDates);
            });
        </script>

        @push('styles')
            <style>
                @media print {
                    .no-print {
                        display: none !important;
                    }

                    .print-break {
                        page-break-before: always;
                    }

                    body {
                        font-size: 12px;
                    }

                    .text-2xl {
                        font-size: 18px !important;
                    }

                    .text-xl {
                        font-size: 16px !important;
                    }
                }
            </style>
        @endpush
</x-app-layout>
