<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Detail Karyawan - {{ $employee->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap dan riwayat presensi karyawan</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('employees.edit', $employee) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data
                </a>
                <a href="{{ route('employees.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Employee Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 bg-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-100 text-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-3xl"></i>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</h1>

                            <!-- Status Badge -->
                            @if($employee->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @elseif($employee->status === 'inactive')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-pause-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Terminated
                                </span>
                            @endif

                            <!-- Face Status -->
                            @if($employee->user->hasFaceEnrolled())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    <i class="fas fa-face-smile mr-1"></i>
                                    Face Enrolled
                                </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                    <i class="fas fa-face-frown mr-1"></i>
                                    No Face Data
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Informasi Personal</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-id-badge w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">ID:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->employee_id }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Email:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->user->email }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->phone ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Informasi Pekerjaan</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Jabatan:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->position }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-building w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Departemen:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->department ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Bergabung:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->join_date->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Jadwal Kerja</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-sun w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Masuk:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->work_start_time->format('H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-moon w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Pulang:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $employee->work_end_time->format('H:i') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-{{ $employee->is_flexible_time ? 'check' : 'times' }} w-4 text-{{ $employee->is_flexible_time ? 'green' : 'red' }}-500 mr-2"></i>
                                        <span class="text-gray-600">Fleksibel:</span>
                                        <span class="ml-2 font-medium text-{{ $employee->is_flexible_time ? 'green' : 'red' }}-600">
                                            {{ $employee->is_flexible_time ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex-shrink-0 space-y-2">
                        @if(!$employee->user->hasFaceEnrolled())
                        <a href="{{ route('face-enrollment.show', $employee) }}"
                           class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-camera mr-2"></i>
                            Enroll Face
                        </a>
                        @endif

                        <a href="{{ route('reports.employee', $employee) }}"
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-chart-line mr-2"></i>
                            Laporan
                        </a>

                        <button onclick="toggleStatus({{ $employee->id }}, '{{ $employee->status }}')"
                                class="block w-full text-center px-4 py-2 bg-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-700 transition-colors">
                            <i class="fas fa-{{ $employee->status === 'active' ? 'pause' : 'play' }} mr-2"></i>
                            {{ $employee->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </div>
                </div>

                <!-- Location Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Lokasi Kerja</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $employee->location->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $employee->location->address }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Koordinat: {{ $employee->location->latitude }}, {{ $employee->location->longitude }}
                                    (Radius: {{ $employee->location->radius }}m)
                                </p>
                            </div>
                            <div class="bg-{{ $employee->location->is_active ? 'green' : 'red' }}-100 text-{{ $employee->location->is_active ? 'green' : 'red' }}-600 p-2 rounded-lg">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($employee->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Catatan</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">{{ $employee->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                    Ringkasan Bulan Ini
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-lg w-12 h-12 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <p class="text-2xl font-bold text-blue-600">{{ $monthlySummary['present_days'] }}</p>
                        <p class="text-sm text-gray-600">Hari Hadir</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg w-12 h-12 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-clock"></i>
                        </div>
                        <p class="text-2xl font-bold text-yellow-600">{{ $monthlySummary['late_days'] }}</p>
                        <p class="text-sm text-gray-600">Hari Terlambat</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-green-100 text-green-600 p-3 rounded-lg w-12 h-12 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-business-time"></i>
                        </div>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($monthlySummary['total_work_hours'], 1) }}</p>
                        <p class="text-sm text-gray-600">Total Jam</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-purple-100 text-purple-600 p-3 rounded-lg w-12 h-12 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $monthlySummary['present_days'] > 0 ? round(($monthlySummary['present_days'] / date('t')) * 100, 1) : 0 }}%
                        </p>
                        <p class="text-sm text-gray-600">Tingkat Hadir</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-history mr-2 text-indigo-600"></i>
                        Presensi Terbaru (10 Hari Terakhir)
                    </h3>
                    <a href="{{ route('reports.employee', $employee) }}"
                       class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
            </div>

            @if($employee->attendances->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($employee->attendances as $attendance)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-100 text-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-600 p-3 rounded-full">
                                <i class="fas fa-{{ $attendance->type == 'clock_in' ? 'sign-in-alt' : 'sign-out-alt' }}"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">
                                    {{ $attendance->type == 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ $attendance->attendance_date->isoFormat('dddd, D MMMM YYYY') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $attendance->location->name ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">
                                {{ $attendance->attendance_time->format('H:i') }}
                            </p>

                            <!-- Status -->
                            @if($attendance->status == 'success')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Berhasil
                                        </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                            @endif

                            <!-- Late Status -->
                            @if($attendance->is_late && $attendance->type == 'clock_in')
                            <p class="text-xs text-red-600 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                +{{ $attendance->late_minutes }}m
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center">
                <div class="bg-gray-100 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-calendar-times text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Presensi</h3>
                <p class="text-gray-600">Karyawan ini belum melakukan presensi.</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        async function toggleStatus(employeeId, currentStatus) {
            const action = currentStatus === 'active' ? 'nonaktifkan' : 'aktifkan';

            if (!confirm(`Apakah Anda yakin ingin ${action} karyawan ini?`)) {
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
    </script>
    @endpush
</x-app-layout>
