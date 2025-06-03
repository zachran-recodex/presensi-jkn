<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-users mr-2 text-blue-600"></i>
                    Manajemen Karyawan
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola data karyawan dan informasi presensi</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('employees.export') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Excel
                </a>
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Karyawan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter & Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari Karyawan
                    </label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nama, email, atau ID..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
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

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('employees.index') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div x-data="{ selectedEmployees: [], selectAll: false, showBulkActions: false }" class="space-y-4">
            <!-- Bulk Action Bar -->
            <div x-show="selectedEmployees.length > 0"
                 x-transition
                 class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-blue-900">
                            <span x-text="selectedEmployees.length"></span> karyawan dipilih
                        </span>
                        <button @click="selectedEmployees = []"
                                class="text-sm text-blue-600 hover:text-blue-800">
                            Batalkan Pilihan
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select x-model="bulkAction"
                                class="border border-blue-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Aksi</option>
                            <option value="activate">Aktifkan</option>
                            <option value="deactivate">Non-aktifkan</option>
                            <option value="delete">Hapus</option>
                        </select>
                        <button @click="executeBulkAction()"
                                :disabled="!bulkAction"
                                class="px-4 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 text-sm">
                            Jalankan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Employee List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Daftar Karyawan
                        </h3>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox"
                                       x-model="selectAll"
                                       @change="toggleSelectAll()"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">Pilih Semua</span>
                            </label>
                            <span class="text-sm text-gray-600">
                                {{ $employees->total() }} karyawan ditemukan
                            </span>
                        </div>
                    </div>
                </div>

                @if($employees->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($employees as $employee)
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <!-- Checkbox -->
                                    <input type="checkbox"
                                           value="{{ $employee->id }}"
                                           x-model="selectedEmployees"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-100 text-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>

                                    <!-- Employee Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-lg font-semibold text-gray-900 truncate">
                                                {{ $employee->user->name }}
                                            </h4>

                                            <!-- Status Badge -->
                                            @if($employee->status === 'active')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Aktif
                                                </span>
                                            @elseif($employee->status === 'inactive')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-pause-circle mr-1"></i>
                                                    Tidak Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    Terminated
                                                </span>
                                            @endif

                                            <!-- Face Enrollment Status -->
                                            @if($employee->user->hasFaceEnrolled())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    <i class="fas fa-face-smile mr-1"></i>
                                                    Face OK
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                    <i class="fas fa-face-frown mr-1"></i>
                                                    No Face
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-id-badge mr-1"></i>
                                                    ID: {{ $employee->employee_id }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $employee->user->email }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-briefcase mr-1"></i>
                                                    {{ $employee->position }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-building mr-1"></i>
                                                    {{ $employee->department ?: 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $employee->location->name }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Bergabung: {{ $employee->join_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex-shrink-0" x-data="{ showDropdown: false }">
                                        <div class="relative">
                                            <button @click="showDropdown = !showDropdown"
                                                    class="inline-flex items-center px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>

                                            <div x-show="showDropdown"
                                                 x-transition
                                                 @click.away="showDropdown = false"
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('employees.show', $employee) }}"
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-eye mr-2 text-blue-500"></i>
                                                        Lihat Detail
                                                    </a>

                                                    <a href="{{ route('employees.edit', $employee) }}"
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-edit mr-2 text-green-500"></i>
                                                        Edit
                                                    </a>

                                                    @if(!$employee->user->hasFaceEnrolled())
                                                        <a href="{{ route('face-enrollment.show', $employee) }}"
                                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-face-smile mr-2 text-purple-500"></i>
                                                            Face Enrollment
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('reports.employee', $employee) }}"
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-chart-line mr-2 text-indigo-500"></i>
                                                        Laporan
                                                    </a>

                                                    <div class="border-t border-gray-100"></div>

                                                    <button onclick="toggleEmployeeStatus({{ $employee->id }}, '{{ $employee->status }}')"
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-{{ $employee->status === 'active' ? 'pause' : 'play' }} mr-2 text-yellow-500"></i>
                                                        {{ $employee->status === 'active' ? 'Non-aktifkan' : 'Aktifkan' }}
                                                    </button>

                                                    @if($employee->status !== 'terminated')
                                                        <button onclick="confirmDelete({{ $employee->id }}, '{{ $employee->user->name }}')"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                            <i class="fas fa-trash mr-2"></i>
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-200">
                        {{ $employees->withQueryString()->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-users text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Karyawan</h3>
                        <p class="text-gray-600 mb-6">Belum ada karyawan yang terdaftar dalam sistem.</p>
                        <a href="{{ route('employees.create') }}"
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Karyawan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select All functionality
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
            const selectAllCheckbox = document.querySelector('input[x-model="selectAll"]');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                // Trigger Alpine.js model update
                checkbox.dispatchEvent(new Event('change'));
            });
        }

        // Toggle Employee Status
        async function toggleEmployeeStatus(employeeId, currentStatus) {
            const action = currentStatus === 'active' ? 'Non-aktifkan' : 'Aktifkan';

            if (!confirm(`Apakah Anda yakin ingin ${action.toLowerCase()} karyawan ini?`)) {
                return;
            }

            showLoading();

            try {
                const response = await fetch(`/employees/${employeeId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                hideLoading();
            }
        }

        // Confirm Delete
        function confirmDelete(employeeId, employeeName) {
            if (confirm(`Apakah Anda yakin ingin menghapus karyawan "${employeeName}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/employees/${employeeId}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Bulk Actions
        async function executeBulkAction() {
            const selectedIds = Array.from(document.querySelectorAll('input[type="checkbox"][value]:checked'))
                .map(cb => cb.value);

            const action = document.querySelector('select[x-model="bulkAction"]').value;

            if (!selectedIds.length || !action) {
                alert('Pilih karyawan dan aksi yang akan dilakukan.');
                return;
            }

            let confirmMessage = '';
            switch(action) {
                case 'activate':
                    confirmMessage = `Aktifkan ${selectedIds.length} karyawan?`;
                    break;
                case 'deactivate':
                    confirmMessage = `Non-aktifkan ${selectedIds.length} karyawan?`;
                    break;
                case 'delete':
                    confirmMessage = `Hapus ${selectedIds.length} karyawan? Tindakan ini tidak dapat dibatalkan.`;
                    break;
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            showLoading();

            try {
                const response = await fetch('/employees/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        action: action,
                        employee_ids: selectedIds
                    })
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert('Gagal melakukan aksi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                hideLoading();
            }
        }
    </script>
    @endpush
</x-app-layout>
