<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Face Enrollment Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
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
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
                                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
                                <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
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

            <!-- API Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status API Biznet Face Recognition</h3>

                    @if(isset($apiCounters['status']) && $apiCounters['status'] === 'error')
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <div>
                                    <p class="text-red-800 font-medium">API Tidak Tersedia</p>
                                    <p class="text-red-700 text-sm mt-1">{{ $apiCounters['message'] ?? 'Koneksi ke API gagal' }}</p>
                                    <button onclick="testApiConnection()"
                                            class="mt-2 bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                        Test Koneksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
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
                                        Test API
                                    </a>
                                    <button onclick="refreshApiStatus()"
                                            class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                        Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Department Statistics -->
            @if(isset($stats['departments']) && count($stats['departments']) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrollment by Department</h3>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter & Pencarian</h3>
                    <form method="GET" action="{{ route('face-enrollment.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan</label>
                                <input type="text"
                                       id="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Nama, email, atau ID karyawan..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                    Filter
                                </button>
                                <a href="{{ route('face-enrollment.index') }}"
                                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Reset
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
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Karyawan</h3>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">
                                Total: {{ $employees->total() }} karyawan
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="exportData()"
                                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    Export CSV
                                </button>
                                <button onclick="refreshStats()"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                    Refresh
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Enrolled
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
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
                                                            Test
                                                        </a>
                                                        <button onclick="reenrollEmployee({{ $employee->id }})"
                                                                class="text-yellow-600 hover:text-yellow-900">
                                                            Re-enroll
                                                        </button>
                                                        <button onclick="deleteEnrollment({{ $employee->id }})"
                                                                class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    @else
                                                        <a href="{{ route('face-enrollment.show', $employee) }}"
                                                           class="text-green-600 hover:text-green-900 font-medium">
                                                            Enroll
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
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
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
                            <svg x-show="type === 'success'" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="type === 'error'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
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
