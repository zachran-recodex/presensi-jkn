<x-app-layout>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Riwayat Presensi - Admin
    </h2>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-gray-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Records</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $attendances->total() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Berhasil</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $attendances->where('status', 'success')->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-times-circle text-red-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Gagal</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $attendances->where('status', 'failed')->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Terlambat</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        {{ $attendances->where('is_late', true)->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter & Pencarian</h3>
                    <form method="GET" action="{{ route('admin.attendance.history') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                                <input type="text"
                                       id="employee"
                                       name="employee"
                                       value="{{ request('employee') }}"
                                       placeholder="Nama atau ID karyawan..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                <select id="location_id"
                                        name="location_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                                <select id="type"
                                        name="type"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua</option>
                                    <option value="clock_in" {{ request('type') === 'clock_in' ? 'selected' : '' }}>Clock In</option>
                                    <option value="clock_out" {{ request('type') === 'clock_out' ? 'selected' : '' }}>Clock Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date"
                                       id="date_from"
                                       name="date_from"
                                       value="{{ request('date_from') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date"
                                       id="date_to"
                                       name="date_to"
                                       value="{{ request('date_to') }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status"
                                        name="status"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua</option>
                                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>

                            <div class="flex items-end space-x-2">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Filter
                                </button>
                                <a href="{{ route('admin.attendance.history') }}"
                                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Presensi</h3>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">
                                Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} record
                            </div>
                            @if($attendances->total() > 0)
                                <button @click="exportData()"
                                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    <i class="fas fa-file-export mr-1"></i>Export CSV
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($attendances->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Karyawan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal/Waktu
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Face Recognition
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Foto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($attendances as $attendance)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($attendance->user->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $attendance->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $attendance->user->employee->employee_id ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div>{{ $attendance->attendance_date->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $attendance->attendance_time->format('H:i:s') }}</div>
                                                @if($attendance->is_late && $attendance->type === 'clock_in')
                                                    <div class="text-xs text-red-600">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        +{{ $attendance->late_minutes }}m
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attendance->type === 'clock_in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $attendance->type === 'clock_in' ? 'Masuk' : 'Pulang' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($attendance->status === 'success')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Berhasil
                                                    </span>
                                                @elseif($attendance->status === 'failed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Gagal
                                                    </span>
                                                    @if($attendance->failure_reason)
                                                        <div class="text-xs text-red-600 mt-1">
                                                            {{ Str::limit($attendance->failure_reason, 50) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($attendance->location)
                                                    <div class="flex items-center">
                                                        <i class="fas fa-map-marker-alt mr-1 {{ $attendance->is_valid_location ? 'text-green-500' : 'text-red-500' }}"></i>
                                                        <div>
                                                            <div class="font-medium">{{ $attendance->location->name }}</div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ number_format($attendance->distance_from_office, 0) }}m dari kantor
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($attendance->face_similarity_score)
                                                    <div class="flex items-center">
                                                        <div class="flex-1">
                                                            <div class="text-xs font-medium">
                                                                {{ number_format($attendance->face_similarity_score * 100, 1) }}%
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                                <div class="h-1.5 rounded-full {{ $attendance->face_similarity_score >= 0.75 ? 'bg-green-600' : 'bg-red-600' }}"
                                                                     style="width: {{ $attendance->face_similarity_score * 100 }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($attendance->photo_path)
                                                    <button @click="showPhoto('{{ route('attendance.photo', $attendance) }}')"
                                                            class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-image text-lg"></i>
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button @click="showDetails({{ $attendance->id }})"
                                                        class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                    <i class="fas fa-info-circle mr-1"></i>Detail
                                                </button>
                                                @if($attendance->user->employee)
                                                    <a href="{{ route('reports.employee', $attendance->user->employee) }}"
                                                       class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-file-alt mr-1"></i>Laporan
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $attendances->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data presensi</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Tidak ada data presensi yang sesuai dengan filter yang dipilih.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div x-data="{ show: false, photoUrl: '' }"
         x-show="show"
         x-cloak
         @show-photo.window="show = true; photoUrl = $event.detail.url"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="show = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Foto Presensi</h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <img :src="photoUrl" alt="Foto Presensi" class="max-w-full h-auto rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-data="{ show: false, loading: false, attendance: null }"
         x-show="show"
         x-cloak
         @show-details.window="show = true; loadDetails($event.detail.id)"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="show = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Presensi</h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div x-show="loading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                        <p class="text-gray-600 mt-2">Loading...</p>
                    </div>

                    <div x-show="!loading && attendance" class="space-y-4">
                        <!-- Detail content will be loaded here -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Karyawan:</span>
                                <span x-text="attendance?.user?.name"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tanggal:</span>
                                <span x-text="attendance?.attendance_date"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">IP Address:</span>
                                <span x-text="attendance?.ip_address"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Device:</span>
                                <span x-text="attendance?.device_info" class="text-xs"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showPhoto(url) {
            window.dispatchEvent(new CustomEvent('show-photo', {
                detail: { url: url }
            }));
        }

        function showDetails(id) {
            window.dispatchEvent(new CustomEvent('show-details', {
                detail: { id: id }
            }));
        }

        function exportData() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', '1');
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        // Auto-refresh every 30 seconds if on today's data
        @if(!request()->hasAny(['date_from', 'date_to']) || (request('date_from') === today() && request('date_to') === today()))
            setInterval(() => {
                window.location.reload();
            }, 30000);
        @endif
    </script>
    @endpush
</x-app-layout>
