<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-history mr-2 text-purple-600"></i>
                    History Presensi (Admin)
                </h2>
                <p class="text-sm text-gray-600 mt-1">Monitoring semua presensi karyawan</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshData()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Advanced Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>
                    Filter & Pencarian
                </h3>
                <button type="button"
                        onclick="resetFilters()"
                        class="text-sm text-gray-600 hover:text-gray-800">
                    Reset Semua Filter
                </button>
            </div>

            <form method="GET" action="{{ route('admin.attendance.history') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Employee Search -->
                    <div class="md:col-span-2">
                        <label for="employee" class="block text-sm font-medium text-gray-700 mb-2">
                            Cari Karyawan
                        </label>
                        <input type="text"
                               id="employee"
                               name="employee"
                               value="{{ request('employee') }}"
                               placeholder="Nama karyawan..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            Dari Tanggal
                        </label>
                        <input type="date"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Sampai Tanggal
                        </label>
                        <input type="date"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Status</option>
                            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Quick Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            onclick="setDateFilter('today')"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Hari Ini
                    </button>
                    <button type="button"
                            onclick="setDateFilter('yesterday')"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Kemarin
                    </button>
                    <button type="button"
                            onclick="setDateFilter('week')"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        7 Hari Terakhir
                    </button>
                    <button type="button"
                            onclick="setDateFilter('month')"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Bulan Ini
                    </button>
                    <button type="button"
                            onclick="setStatusFilter('failed')"
                            class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                        Hanya Yang Gagal
                    </button>
                    <button type="button"
                            onclick="setStatusFilter('late')"
                            class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                        Hanya Yang Terlambat
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Hasil Pencarian
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Ditemukan {{ $attendances->total() }} record presensi
                        @if(request('date_from') && request('date_to'))
                            dari {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
                            sampai {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
                        @endif
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <!-- Real-time indicator -->
                    <div class="flex items-center text-green-600">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                        <span class="text-sm">Live</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance History Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Karyawan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal & Waktu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Face Recognition
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Detail
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <!-- Employee Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full mr-3">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attendance->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->user->employee->employee_id ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $attendance->user->employee->department ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date & Time -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $attendance->attendance_date->format('d M Y') }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $attendance->attendance_time->format('H:i:s') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $attendance->attendance_date->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                            {{ $attendance->type == 'clock_in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            <i class="fas fa-{{ $attendance->type == 'clock_in' ? 'sign-in-alt' : 'sign-out-alt' }} mr-1"></i>
                                            {{ $attendance->type == 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                        </span>

                                    @if($attendance->is_late && $attendance->type == 'clock_in')
                                        <div class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            +{{ $attendance->late_minutes }}m
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->status == 'success')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Berhasil
                                            </span>
                                    @elseif($attendance->status == 'failed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Gagal
                                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                    @endif

                                    @if($attendance->failure_reason)
                                        <div class="text-xs text-red-600 mt-1">
                                            {{ $attendance->failure_reason }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Location -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $attendance->location->name ?? 'N/A' }}
                                    </div>
                                    @if($attendance->distance_from_office)
                                        <div class="text-xs text-gray-500">
                                            {{ number_format($attendance->distance_from_office, 0) }}m dari kantor
                                        </div>
                                    @endif
                                    <div class="text-xs {{ $attendance->is_valid_location ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $attendance->is_valid_location ? '✓ Lokasi Valid' : '✗ Lokasi Invalid' }}
                                    </div>
                                </td>

                                <!-- Face Recognition -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->face_similarity_score)
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($attendance->face_similarity_score * 100, 1) }}%
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="bg-{{ $attendance->face_similarity_score >= 0.75 ? 'green' : 'red' }}-600 h-1.5 rounded-full"
                                                 style="width: {{ $attendance->face_similarity_score * 100 }}%"></div>
                                        </div>
                                        <div class="text-xs {{ $attendance->face_similarity_score >= 0.75 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                            {{ $attendance->face_similarity_score >= 0.75 ? '✓ Valid' : '✗ Invalid' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">No Data</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap" x-data="{ showDetails: false }">
                                    <button @click="showDetails = !showDetails"
                                            class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Detail
                                    </button>

                                    <!-- Detail Dropdown -->
                                    <div x-show="showDetails"
                                         x-transition
                                         @click.away="showDetails = false"
                                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-10">

                                        <!-- Photo -->
                                        @if($attendance->photo_path)
                                            <div class="mb-3">
                                                <label class="text-xs font-medium text-gray-700">Foto Presensi:</label>
                                                <img src="{{ Storage::url($attendance->photo_path) }}"
                                                     alt="Foto Presensi"
                                                     class="w-full h-32 object-cover rounded-lg mt-1 border border-gray-200 cursor-pointer"
                                                     onclick="showPhotoModal('{{ Storage::url($attendance->photo_path) }}')">
                                            </div>
                                        @endif

                                        <!-- GPS Info -->
                                        <div class="mb-3">
                                            <label class="text-xs font-medium text-gray-700">Koordinat GPS:</label>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $attendance->latitude }}, {{ $attendance->longitude }}
                                            </p>
                                            <button onclick="viewOnMap({{ $attendance->latitude }}, {{ $attendance->longitude }})"
                                                    class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                Lihat di Maps
                                            </button>
                                        </div>

                                        <!-- Device Info -->
                                        <div class="mb-3">
                                            <label class="text-xs font-medium text-gray-700">Device Info:</label>
                                            <p class="text-xs text-gray-500 mt-1">{{ $attendance->device_info }}</p>
                                            <p class="text-xs text-gray-500">IP: {{ $attendance->ip_address }}</p>
                                        </div>

                                        <!-- Notes -->
                                        @if($attendance->notes)
                                            <div class="mb-3">
                                                <label class="text-xs font-medium text-gray-700">Catatan:</label>
                                                <p class="text-sm text-gray-600 mt-1">{{ $attendance->notes }}</p>
                                            </div>
                                        @endif

                                        <!-- Face Recognition Details -->
                                        @if($attendance->face_recognition_result)
                                            <div class="mb-3">
                                                <label class="text-xs font-medium text-gray-700">Face Recognition Result:</label>
                                                <pre class="text-xs text-gray-600 mt-1 bg-gray-100 p-2 rounded overflow-x-auto">{{ json_encode($attendance->face_recognition_result, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $attendances->withQueryString()->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-600 mb-6">Tidak ada data presensi yang sesuai dengan filter yang dipilih.</p>
                    <button onclick="resetFilters()"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Filter
                    </button>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Quick filter functions
            function setDateFilter(period) {
                const today = new Date();
                const dateFrom = document.getElementById('date_from');
                const dateTo = document.getElementById('date_to');

                switch(period) {
                    case 'today':
                        dateFrom.value = today.toISOString().split('T')[0];
                        dateTo.value = today.toISOString().split('T')[0];
                        break;
                    case 'yesterday':
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        dateFrom.value = yesterday.toISOString().split('T')[0];
                        dateTo.value = yesterday.toISOString().split('T')[0];
                        break;
                    case 'week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        dateFrom.value = weekAgo.toISOString().split('T')[0];
                        dateTo.value = today.toISOString().split('T')[0];
                        break;
                    case 'month':
                        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                        dateFrom.value = firstDay.toISOString().split('T')[0];
                        dateTo.value = today.toISOString().split('T')[0];
                        break;
                }
            }

            function setStatusFilter(status) {
                const statusSelect = document.getElementById('status');
                if (status === 'late') {
                    // For late filter, we'll need to handle this differently
                    // For now, just set to success and let user know to check for late entries
                    statusSelect.value = 'success';
                } else {
                    statusSelect.value = status;
                }
            }

            function resetFilters() {
                document.getElementById('employee').value = '';
                document.getElementById('date_from').value = '';
                document.getElementById('date_to').value = '';
                document.getElementById('status').value = '';
            }

            function refreshData() {
                showLoading();
                location.reload();
            }

            function viewOnMap(lat, lng) {
                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                window.open(mapsUrl, '_blank');
            }

            function showPhotoModal(imageSrc) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
                modal.innerHTML = `
                <div class="max-w-4xl max-h-full p-4 relative">
                    <img src="${imageSrc}" class="max-w-full max-h-full object-contain rounded-lg">
                    <button onclick="this.parentElement.parentElement.remove()"
                            class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300 bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.remove();
                    }
                });

                document.body.appendChild(modal);
            }

            // Auto-refresh every 30 seconds for today's data
            @if(!request('date_from') || \Carbon\Carbon::parse(request('date_from'))->isToday())
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    // Silently refresh the page
                    window.location.reload();
                }
            }, 30000); // 30 seconds
            @endif

            // Real-time updates notification
            function showUpdateNotification() {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-sync-alt mr-2 animate-spin"></i>
                    <span>Data presensi diperbarui</span>
                </div>
            `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey) {
                    switch(e.key) {
                        case 'f':
                            e.preventDefault();
                            document.getElementById('employee').focus();
                            break;
                        case 'r':
                            e.preventDefault();
                            refreshData();
                            break;
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
