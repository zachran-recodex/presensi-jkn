<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-calendar-day mr-2 text-blue-600"></i>
                    Laporan Harian
                </h2>
                <p class="text-sm text-gray-600 mt-1">Presensi karyawan per hari</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('reports.export-daily', ['date' => $date->format('Y-m-d')]) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Excel
                </a>
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('reports.daily') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal
                    </label>
                    <input type="date"
                           id="date"
                           name="date"
                           value="{{ request('date', $date->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Departemen
                    </label>
                    <select id="department"
                            name="department"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi
                    </label>
                    <select id="location_id"
                            name="location_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('reports.daily') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['total_employees'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Hadir</p>
                        <p class="text-2xl font-bold text-green-600">{{ $summary['present_count'] }}</p>
                        <p class="text-xs text-green-600">
                            {{ $summary['total_employees'] > 0 ? round(($summary['present_count'] / $summary['total_employees']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 text-red-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tidak Hadir</p>
                        <p class="text-2xl font-bold text-red-600">{{ $summary['absent_count'] }}</p>
                        <p class="text-xs text-red-600">
                            {{ $summary['total_employees'] > 0 ? round(($summary['absent_count'] / $summary['total_employees']) * 100, 1) : 0 }}%
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
                        <p class="text-2xl font-bold text-yellow-600">{{ $summary['late_count'] }}</p>
                        <p class="text-xs text-yellow-600">
                            {{ $summary['present_count'] > 0 ? round(($summary['late_count'] / $summary['present_count']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Presensi {{ $date->isoFormat('dddd, D MMMM YYYY') }}
                    </h3>
                    <div class="text-sm text-gray-600">
                        {{ $attendances->count() }} dari {{ $summary['total_employees'] }} karyawan
                    </div>
                </div>
            </div>

            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Karyawan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departemen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Masuk
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Pulang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Jam
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $userId => $userAttendances)
                            @php
                                $clockIn = $userAttendances->where('type', 'clock_in')->first();
                                $clockOut = $userAttendances->where('type', 'clock_out')->first();
                                $employee = $clockIn->user->employee ?? null;

                                $totalHours = 0;
                                if ($clockIn && $clockOut) {
                                    $start = \Carbon\Carbon::parse($clockIn->attendance_time);
                                    $end = \Carbon\Carbon::parse($clockOut->attendance_time);
                                    $totalHours = $start->diffInHours($end, true);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $clockIn->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $employee->employee_id ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->department ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($clockIn)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $clockIn->attendance_time->format('H:i:s') }}
                                        </div>
                                        @if($clockIn->is_late)
                                            <div class="text-xs text-red-600">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                +{{ $clockIn->late_minutes }}m
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600">
                                                <i class="fas fa-check mr-1"></i>
                                                Tepat waktu
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $clockOut ? $clockOut->attendance_time->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $totalHours > 0 ? number_format($totalHours, 1) . 'h' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $clockIn->location->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Presensi</h3>
                    <p class="text-gray-600">Tidak ada data presensi untuk tanggal yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-refresh setiap 5 menit jika melihat hari ini
            @if($date->isToday())
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    location.reload();
                }
            }, 300000); // 5 minutes
            @endif

            // Quick date navigation
            document.addEventListener('DOMContentLoaded', function() {
                const dateInput = document.getElementById('date');

                // Add keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && e.key === 'ArrowLeft') {
                        // Previous day
                        const currentDate = new Date(dateInput.value);
                        currentDate.setDate(currentDate.getDate() - 1);
                        dateInput.value = currentDate.toISOString().split('T')[0];
                        dateInput.form.submit();
                    } else if (e.ctrlKey && e.key === 'ArrowRight') {
                        // Next day
                        const currentDate = new Date(dateInput.value);
                        currentDate.setDate(currentDate.getDate() + 1);
                        dateInput.value = currentDate.toISOString().split('T')[0];
                        dateInput.form.submit();
                    } else if (e.ctrlKey && e.key === 'Home') {
                        // Today
                        dateInput.value = new Date().toISOString().split('T')[0];
                        dateInput.form.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
