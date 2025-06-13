<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-id-card mr-2"></i>{{ __('Face Enrollment Management') }}
            </h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-gray-400 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_employees'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Sudah Enrolled</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['enrolled_employees'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Belum Enrolled</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_employees'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Enrollment Rate</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['enrollment_rate'] }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Status
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-plug mr-2"></i>Status API Biznet Face Recognition
                    </h3>

                    @if(isset($apiCounters['status']) && $apiCounters['status'] === 'error')
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-red-400 mr-2 text-xl"></i>
                                <div>
                                    <p class="text-red-800 font-medium">API Tidak Tersedia</p>
                                    <p class="text-red-700 text-sm mt-1">{{ $apiCounters['message'] ?? 'Koneksi ke API gagal' }}</p>
                                    <button onclick="testApiConnection()"
                                            class="mt-2 bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                        <i class="fas fa-sync-alt mr-1"></i>Test Koneksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-2 text-xl"></i>
                                <div class="flex-1">
                                    <p class="text-green-800 font-medium">API Connected</p>
                                    @if(isset($apiCounters['remaining_limit']))
                                        <p class="text-green-700 text-sm">
                                            Remaining quota:
                                            @if(is_array($apiCounters['remaining_limit']))
                                                {{ json_encode($apiCounters['remaining_limit']) }}
                                            @else
                                                {{ $apiCounters['remaining_limit'] }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('face-api-test.index') }}"
                                       class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                        <i class="fas fa-vial mr-1"></i>Test API
                                    </a>
                                    <button onclick="refreshApiStatus()"
                                            class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
             -->

            <!-- Department Statistics -->
            @if(isset($stats['departments']) && count($stats['departments']) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-building mr-2"></i>Enrollment by Department
                        </h3>
                        <div class="space-y-3">
                            @foreach($stats['departments'] as $dept)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $dept['department'] ?: 'No Department' }}</span>
                                        <span class="text-sm text-gray-600 ml-2">({{ $dept['enrolled'] }}/{{ $dept['total'] }})</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $dept['rate'] >= 80 ? 'bg-green-600' : ($dept['rate'] >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                                 style="width: {{ $dept['rate'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ $dept['rate'] }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-filter mr-2"></i>Filter & Pencarian
                    </h3>
                    <form method="GET" action="{{ route('face-enrollment.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text"
                                           id="search"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="Nama, email, atau ID karyawan..."
                                           class="pl-10 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div>
                                <label for="enrollment_status" class="block text-sm font-medium text-gray-700 mb-1">Status Enrollment</label>
                                <select id="enrollment_status"
                                        name="enrollment_status"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua</option>
                                    <option value="enrolled" {{ request('enrollment_status') === 'enrolled' ? 'selected' : '' }}>Sudah Enrolled</option>
                                    <option value="not_enrolled" {{ request('enrollment_status') === 'not_enrolled' ? 'selected' : '' }}>Belum Enrolled</option>
                                </select>
                            </div>

                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select id="department"
                                        name="department"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                            {{ $dept ?: 'No Department' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end space-x-2">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fas fa-filter mr-1"></i>Filter
                                </button>
                                <a href="{{ route('face-enrollment.index') }}"
                                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    <i class="fas fa-sync-alt mr-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employee List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-list mr-2"></i>Daftar Karyawan
                        </h3>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-users mr-1"></i>Total: {{ $employees->total() }} karyawan
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="refreshStats()"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($employees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Karyawan
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department/Position
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Enrollment
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Face ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-centers text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($employees as $employee)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $employee->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $employee->employee_id }} • {{ $employee->user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $employee->position }}</div>
                                                <div class="text-gray-500">{{ $employee->department ?: 'No Department' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $employee->location->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($employee->user->hasFaceEnrolled())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                                        Enrolled
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times-circle mr-1 text-xs"></i>
                                                        Belum Enrolled
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($employee->user->face_id)
                                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                                        {{ Str::limit($employee->user->face_id, 20) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if($employee->user->hasFaceEnrolled())
                                                        <a href="{{ route('face-enrollment.show', $employee) }}"
                                                           class="text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-vial mr-1"></i>Test
                                                        </a>
                                                        <button onclick="reenrollEmployee({{ $employee->id }})"
                                                                class="text-yellow-600 hover:text-yellow-900">
                                                            <i class="fas fa-sync-alt mr-1"></i>Re-enroll
                                                        </button>
                                                        <button onclick="deleteEnrollment({{ $employee->id }})"
                                                                class="text-red-600 hover:text-red-900">
                                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                                        </button>
                                                    @else
                                                        <a href="{{ route('face-enrollment.show', $employee) }}"
                                                           class="text-green-600 hover:text-green-900 font-medium">
                                                            <i class="fas fa-user-plus mr-1"></i>Enroll
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-users-slash text-gray-400 text-4xl mb-4"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada karyawan ditemukan</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'enrollment_status', 'department']))
                                    Tidak ada karyawan yang sesuai dengan filter.
                                @else
                                    Belum ada karyawan aktif dalam sistem.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div x-data="{ show: false, title: '', message: '', type: 'success', details: null }"
         x-show="show"
         x-cloak
         @enrollment-result.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type; details = $event.detail.details"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div :class="type === 'success' ? 'bg-green-100' : 'bg-red-100'" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i x-show="type === 'success'" class="fas fa-check text-green-600 text-xl"></i>
                            <i x-show="type === 'error'" class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                                <div x-show="details" class="mt-3 text-xs text-gray-600">
                                    <template x-if="details">
                                        <div>
                                            <p x-show="details.employee_name"><strong>Nama:</strong> <span x-text="details.employee_name"></span></p>
                                            <p x-show="details.employee_id"><strong>ID:</strong> <span x-text="details.employee_id"></span></p>
                                            <p x-show="details.enrolled_at"><strong>Waktu:</strong> <span x-text="details.enrolled_at"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="show = false; if(type === 'success') location.reload()"
                            type="button"
                            :class="type === 'success' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Re-enroll employee
        async function reenrollEmployee(employeeId) {
            if (!confirm('Yakin ingin melakukan re-enrollment? Wajah lama akan dihapus.')) {
                return;
            }

            window.location.href = `/face-enrollment/${employeeId}?action=reenroll`;
        }

        // Delete enrollment
        async function deleteEnrollment(employeeId) {
            if (!confirm('Yakin ingin menghapus enrollment wajah? Karyawan tidak akan bisa melakukan presensi.')) {
                return;
            }

            try {
                const response = await fetch(`/face-enrollment/${employeeId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    window.dispatchEvent(new CustomEvent('enrollment-result', {
                        detail: {
                            type: 'success',
                            title: 'Berhasil!',
                            message: result.message,
                            details: result.data
                        }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('enrollment-result', {
                        detail: {
                            type: 'error',
                            title: 'Gagal!',
                            message: result.message
                        }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('enrollment-result', {
                    detail: {
                        type: 'error',
                        title: 'Error!',
                        message: 'Terjadi kesalahan jaringan.'
                    }
                }));
            }
        }

        // Test API connection
        async function testApiConnection() {
            try {
                const response = await fetch('/face-api-test/connection', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                window.dispatchEvent(new CustomEvent('enrollment-result', {
                    detail: {
                        type: result.success ? 'success' : 'error',
                        title: result.success ? 'API Connected!' : 'API Error!',
                        message: result.message
                    }
                }));

                if (result.success) {
                    setTimeout(() => location.reload(), 2000);
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('enrollment-result', {
                    detail: {
                        type: 'error',
                        title: 'Error!',
                        message: 'Gagal test koneksi API.'
                    }
                }));
            }
        }

        // Refresh API status
        async function refreshApiStatus() {
            location.reload();
        }

        // Refresh stats
        async function refreshStats() {
            try {
                const response = await fetch('/face-enrollment/stats');
                const result = await response.json();

                if (result.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Failed to refresh stats:', error);
            }
        }

        // Export data
        function exportData() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', '1');
            window.location.href = `/face-enrollment/export?${params.toString()}`;
        }

        // Auto-refresh every 5 minutes
        setInterval(() => {
            if (document.hidden) return; // Don't refresh if tab is not visible
            refreshStats();
        }, 300000);
    </script>
    @endpush
</x-app-layout>
