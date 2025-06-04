<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                    Dashboard Laporan
                </h2>
                <p class="text-sm text-gray-600 mt-1">Overview dan analisis kehadiran karyawan</p>
            </div>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Monthly Statistics -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl shadow-lg text-white">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Statistik Bulan Ini
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">{{ $monthlyStats['total_working_days'] }}</div>
                        <div class="text-sm opacity-90">Hari Kerja</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">{{ $monthlyStats['total_attendances'] }}</div>
                        <div class="text-sm opacity-90">Total Presensi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">{{ $monthlyStats['late_attendances'] }}</div>
                        <div class="text-sm opacity-90">Terlambat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-2">{{ $monthlyStats['absent_count'] }}</div>
                        <div class="text-sm opacity-90">Tidak Hadir</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Report Access -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Daily Report -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">Hari Ini</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMM') }}</p>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Harian</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Lihat kehadiran karyawan hari ini dengan detail jam masuk dan pulang
                    </p>
                    <a href="{{ route('reports.daily') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Lihat Laporan
                    </a>
                </div>
            </div>

            <!-- Monthly Report -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">Bulanan</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</p>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Bulanan</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Analisis tingkat kehadiran dan kinerja karyawan per bulan
                    </p>
                    <a href="{{ route('reports.monthly') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>
                        Lihat Laporan
                    </a>
                </div>
            </div>

            <!-- Custom Report -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 text-purple-600 p-3 rounded-lg">
                            <i class="fas fa-cog text-xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">Custom</p>
                            <p class="text-sm text-gray-600">Filter & Export</p>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Kustom</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Buat laporan dengan filter tanggal, departemen, dan kriteria lainnya
                    </p>
                    <button onclick="openCustomReportModal()"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Buat Laporan
                    </button>
                </div>
            </div>
        </div>

        <!-- Department Performance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-building mr-2 text-green-600"></i>
                    Performa Departemen
                </h3>
                <p class="text-sm text-gray-600 mt-1">Tingkat kehadiran per departemen bulan ini</p>
            </div>

            @if(count($departmentStats) > 0)
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($departmentStats as $dept)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $dept['department'] ?: 'Tidak Ada Departemen' }}</h4>
                                        <p class="text-sm text-gray-600">{{ $dept['employee_count'] }} karyawan</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-gray-900">{{ $dept['attendance_count'] }}</p>
                                        <p class="text-xs text-gray-600">Total Presensi</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-{{ $dept['attendance_rate'] >= 80 ? 'green' : ($dept['attendance_rate'] >= 60 ? 'yellow' : 'red') }}-600">
                                            {{ $dept['attendance_rate'] }}%
                                        </p>
                                        <p class="text-xs text-gray-600">Tingkat Hadir</p>
                                    </div>
                                    <div class="w-32">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-{{ $dept['attendance_rate'] >= 80 ? 'green' : ($dept['attendance_rate'] >= 60 ? 'yellow' : 'red') }}-600 h-2 rounded-full"
                                                 style="width: {{ $dept['attendance_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-600">Data departemen akan muncul setelah ada presensi karyawan.</p>
                </div>
            @endif
        </div>

        <!-- Location Performance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                    Performa Lokasi
                </h3>
                <p class="text-sm text-gray-600 mt-1">Tingkat kehadiran per lokasi kantor bulan ini</p>
            </div>

            @if(count($locationStats) > 0)
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($locationStats as $loc)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-red-100 text-red-600 p-2 rounded-lg">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $loc['location']->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $loc['employee_count'] }} karyawan</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-{{ $loc['attendance_rate'] >= 80 ? 'green' : ($loc['attendance_rate'] >= 60 ? 'yellow' : 'red') }}-600">
                                        {{ $loc['attendance_rate'] }}%
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $loc['attendance_count'] }} presensi</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-600">Data lokasi akan muncul setelah ada presensi karyawan.</p>
                </div>
            @endif
        </div>

        <!-- Export Options -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-download mr-2 text-indigo-600"></i>
                    Export Laporan
                </h3>
                <p class="text-sm text-gray-600 mt-1">Download laporan dalam berbagai format</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('reports.export-daily', ['date' => date('Y-m-d')]) }}"
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-excel text-green-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Export Harian</p>
                                <p class="text-sm text-gray-600">Excel format</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </a>

                    <a href="{{ route('reports.export-monthly', ['month' => date('Y-m')]) }}"
                       class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-excel text-green-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Export Bulanan</p>
                                <p class="text-sm text-gray-600">Excel format</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </a>

                    <button onclick="openExportModal()"
                            class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-cog text-purple-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Export Custom</p>
                                <p class="text-sm text-gray-600">Dengan filter</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Report Modal -->
    <div x-data="{ showModal: false }" x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Custom Report</h3>
                <form method="GET" action="{{ route('reports.daily') }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="date_from" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                            <input type="date" name="date_to" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                            <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">Semua Departemen</option>
                                @foreach($departmentStats as $dept)
                                    <option value="{{ $dept['department'] }}">{{ $dept['department'] ?: 'Tidak Ada Departemen' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openCustomReportModal() {
                Alpine.store('modal', { showModal: true });
            }

            function openExportModal() {
                // For now, redirect to daily report with custom export
                window.location.href = '{{ route("reports.daily") }}';
            }
        </script>
    @endpush
</x-app-layout>
