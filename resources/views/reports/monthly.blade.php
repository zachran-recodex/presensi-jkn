<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-calendar-alt mr-2 text-green-600"></i>
                    Laporan Bulanan
                </h2>
                <p class="text-sm text-gray-600 mt-1">Analisis kehadiran karyawan per bulan</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('reports.export-monthly', ['month' => $month->format('Y-m')]) }}"
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
            <form method="GET" action="{{ route('reports.monthly') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                        Bulan & Tahun
                    </label>
                    <input type="month"
                           id="month"
                           name="month"
                           value="{{ request('month', $month->format('Y-m')) }}"
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
                    <a href="{{ route('reports.monthly') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Monthly Overview -->
        <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-xl shadow-lg text-white p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-chart-bar mr-2"></i>
                Overview {{ $month->isoFormat('MMMM YYYY') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">{{ count($reportData) }}</div>
                    <div class="text-sm opacity-90">Total Karyawan</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">
                        {{ collect($reportData)->avg('summary.present_days') ? round(collect($reportData)->avg('summary.present_days'), 1) : 0 }}
                    </div>
                    <div class="text-sm opacity-90">Rata-rata Hari Hadir</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">
                        {{ collect($reportData)->avg('attendance_rate') ? round(collect($reportData)->avg('attendance_rate'), 1) : 0 }}%
                    </div>
                    <div class="text-sm opacity-90">Tingkat Kehadiran</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">
                        {{ collect($reportData)->sum('summary.total_work_hours') ? round(collect($reportData)->sum('summary.total_work_hours'), 0) : 0 }}
                    </div>
                    <div class="text-sm opacity-90">Total Jam Kerja</div>
                </div>
            </div>
        </div>

        <!-- Employee Report Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Laporan Karyawan - {{ $month->isoFormat('MMMM YYYY') }}
                    </h3>
                    <div class="text-sm text-gray-600">
                        {{ count($reportData) }} karyawan
                    </div>
                </div>
            </div>

            @if(count($reportData) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full" x-data="{ sortField: 'name', sortDirection: 'asc' }">
                        <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                @click="sort('name')">
                                <div class="flex items-center">
                                    Karyawan
                                    <i class="fas fa-sort ml-1"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departemen
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                @click="sort('present_days')">
                                <div class="flex items-center justify-center">
                                    Hari Hadir
                                    <i class="fas fa-sort ml-1"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hari Terlambat
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Jam
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                @click="sort('attendance_rate')">
                                <div class="flex items-center justify-center">
                                    Tingkat Hadir
                                    <i class="fas fa-sort ml-1"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Performance
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reportData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $data['employee']->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $data['employee']->employee_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $data['employee']->department ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $data['summary']['present_days'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        dari {{ $data['summary']['total_days'] }} hari
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $data['summary']['late_days'] }}
                                    </div>
                                    @if($data['summary']['present_days'] > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ round(($data['summary']['late_days'] / $data['summary']['present_days']) * 100, 1) }}%
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ number_format($data['summary']['total_work_hours'], 1) }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="text-sm font-medium
                                                {{ $data['attendance_rate'] >= 90 ? 'text-green-600' :
                                                   ($data['attendance_rate'] >= 80 ? 'text-yellow-600' :
                                                   ($data['attendance_rate'] >= 70 ? 'text-orange-600' : 'text-red-600')) }}">
                                            {{ $data['attendance_rate'] }}%
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="
                                                {{ $data['attendance_rate'] >= 90 ? 'bg-green-600' :
                                                   ($data['attendance_rate'] >= 80 ? 'bg-yellow-600' :
                                                   ($data['attendance_rate'] >= 70 ? 'bg-orange-600' : 'bg-red-600')) }}
                                                h-1.5 rounded-full"
                                             style="width: {{ $data['attendance_rate'] }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($data['attendance_rate'] >= 90)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-star mr-1"></i>
                                                Excellent
                                            </span>
                                    @elseif($data['attendance_rate'] >= 80)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-thumbs-up mr-1"></i>
                                                Good
                                            </span>
                                    @elseif($data['attendance_rate'] >= 70)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Fair
                                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Poor
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('reports.employee', $data['employee']) }}?start_date={{ $month->startOfMonth()->format('Y-m-d') }}&end_date={{ $month->endOfMonth()->format('Y-m-d') }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Performance Summary -->
                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @php
                            $excellentCount = collect($reportData)->where('attendance_rate', '>=', 90)->count();
                            $goodCount = collect($reportData)->where('attendance_rate', '>=', 80)->where('attendance_rate', '<', 90)->count();
                            $fairCount = collect($reportData)->where('attendance_rate', '>=', 70)->where('attendance_rate', '<', 80)->count();
                            $poorCount = collect($reportData)->where('attendance_rate', '<', 70)->count();
                            $total = count($reportData);
                        @endphp

                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $excellentCount }}</div>
                            <div class="text-sm text-gray-600">Excellent (≥90%)</div>
                            <div class="text-xs text-gray-500">{{ $total > 0 ? round(($excellentCount / $total) * 100, 1) : 0 }}%</div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $goodCount }}</div>
                            <div class="text-sm text-gray-600">Good (80-89%)</div>
                            <div class="text-xs text-gray-500">{{ $total > 0 ? round(($goodCount / $total) * 100, 1) : 0 }}%</div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $fairCount }}</div>
                            <div class="text-sm text-gray-600">Fair (70-79%)</div>
                            <div class="text-xs text-gray-500">{{ $total > 0 ? round(($fairCount / $total) * 100, 1) : 0 }}%</div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $poorCount }}</div>
                            <div class="text-sm text-gray-600">Poor (<70%)</div>
                            <div class="text-xs text-gray-500">{{ $total > 0 ? round(($poorCount / $total) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-600">Tidak ada data karyawan untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Sort functionality
            function sort(field) {
                // This would require additional Alpine.js implementation
                // For now, we'll implement basic client-side sorting
                console.log('Sorting by:', field);
            }

            // Print report
            function printReport() {
                window.print();
            }

            // Quick month navigation
            document.addEventListener('DOMContentLoaded', function() {
                const monthInput = document.getElementById('month');

                // Add keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && e.key === 'ArrowLeft') {
                        // Previous month
                        const currentDate = new Date(monthInput.value + '-01');
                        currentDate.setMonth(currentDate.getMonth() - 1);
                        monthInput.value = currentDate.toISOString().slice(0, 7);
                        monthInput.form.submit();
                    } else if (e.ctrlKey && e.key === 'ArrowRight') {
                        // Next month
                        const currentDate = new Date(monthInput.value + '-01');
                        currentDate.setMonth(currentDate.getMonth() + 1);
                        monthInput.value = currentDate.toISOString().slice(0, 7);
                        monthInput.form.submit();
                    } else if (e.ctrlKey && e.key === 'Home') {
                        // Current month
                        const now = new Date();
                        monthInput.value = now.toISOString().slice(0, 7);
                        monthInput.form.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
