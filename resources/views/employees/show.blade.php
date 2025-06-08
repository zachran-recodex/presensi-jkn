<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <i class="fas fa-users mr-2"></i>
                                Karyawan
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm font-medium text-gray-500">{{ $employee->user->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Header Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-2xl font-bold text-blue-800">{{ strtoupper(substr($employee->user->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</h3>
                                <p class="text-gray-600 text-lg">{{ $employee->position }}</p>
                                <div class="mt-2">
                                    @if($employee->status === 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    @elseif($employee->status === 'inactive')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            Tidak Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Terminated
                                        </span>
                                    @endif

                                    @if($employee->user->hasFaceEnrolled())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 ml-2">
                                            <i class="fas fa-user-check mr-1"></i>
                                            Face Enrolled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 ml-2">
                                            <i class="fas fa-user-times mr-1"></i>
                                            Not Enrolled
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('face-enrollment.show', $employee) }}"
                               class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 flex items-center space-x-2">
                                <i class="fas fa-user-cog"></i>
                                <span>Face Enrollment</span>
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}"
                               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center space-x-2">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <a href="{{ route('employees.index') }}"
                               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 flex items-center space-x-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Employee Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Karyawan</h3>

                            <div class="space-y-6">
                                <!-- Account Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informasi Akun</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Username</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->user->username }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->user->email }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status Akun</label>
                                            <p class="mt-1 text-sm {{ $employee->user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $employee->user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Face ID</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->user->face_id ?: 'Belum terdaftar' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Details -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Detail Karyawan</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">ID Karyawan</label>
                                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $employee->employee_id }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->phone ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Departemen</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->department ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal Bergabung</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->join_date->format('d F Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Work Location & Schedule -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Lokasi & Jadwal Kerja</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Lokasi Kantor</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $employee->location->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $employee->location->address }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Jam Kerja</label>
                                            <p class="mt-1 text-sm text-gray-900">
                                                {{ $employee->work_start_time->format('H:i') }} - {{ $employee->work_end_time->format('H:i') }}
                                                @if($employee->is_flexible_time)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">
                                                        Fleksibel
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if($employee->notes)
                                    <!-- Notes -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Catatan</h4>
                                        <div class="p-3 bg-white rounded-md border">
                                            <p class="text-sm text-gray-900">{{ $employee->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Presensi Bulan Ini</h3>

                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-check text-blue-600 mr-3"></i>
                                            <span class="text-blue-800 font-medium">Total Hari Kerja</span>
                                        </div>
                                        <span class="text-2xl font-bold text-blue-800">{{ $monthlySummary['total_days'] }}</span>
                                    </div>
                                </div>

                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-user-check text-green-600 mr-3"></i>
                                            <span class="text-green-800 font-medium">Hari Hadir</span>
                                        </div>
                                        <span class="text-2xl font-bold text-green-800">{{ $monthlySummary['present_days'] }}</span>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                                            <span class="text-yellow-800 font-medium">Hari Terlambat</span>
                                        </div>
                                        <span class="text-2xl font-bold text-yellow-800">{{ $monthlySummary['late_days'] }}</span>
                                    </div>
                                </div>

                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-stopwatch text-purple-600 mr-3"></i>
                                            <span class="text-purple-800 font-medium">Total Jam Kerja</span>
                                        </div>
                                        <span class="text-2xl font-bold text-purple-800">{{ number_format($monthlySummary['total_work_hours'], 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 text-center">
                                <a href="{{ route('reports.employee', $employee) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    Lihat Laporan Lengkap
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                            <div class="space-y-3">
                                <a href="{{ route('face-enrollment.show', $employee) }}"
                                   class="w-full bg-purple-600 text-white px-4 py-3 rounded-md hover:bg-purple-700 flex items-center justify-center space-x-2 transition duration-150">
                                    <i class="fas fa-user-cog"></i>
                                    <span>Kelola Face Enrollment</span>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}"
                                   class="w-full bg-indigo-600 text-white px-4 py-3 rounded-md hover:bg-indigo-700 flex items-center justify-center space-x-2 transition duration-150">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit Data Karyawan</span>
                                </a>
                                <form action="{{ route('employees.toggle-status', $employee) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin mengubah status karyawan ini?')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-600 text-white px-4 py-3 rounded-md hover:bg-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-700 flex items-center justify-center space-x-2 transition duration-150">
                                        <i class="fas fa-{{ $employee->status === 'active' ? 'pause' : 'play' }}"></i>
                                        <span>{{ $employee->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }} Karyawan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Presensi Terbaru</h3>
                        <a href="{{ route('attendance.history', ['employee' => $employee->user->name]) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Lihat Semua
                        </a>
                    </div>

                    @if($employee->attendances->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employee->attendances->take(10) as $attendance)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->attendance_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attendance->type === 'clock_in')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-sign-in-alt mr-1"></i>
                                                        Clock In
                                                    </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                                        Clock Out
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->attendance_time->format('H:i:s') }}
                                            @if($attendance->type === 'clock_in' && $attendance->is_late)
                                                <div class="text-xs text-red-600">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Terlambat {{ $attendance->late_minutes }} menit
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->location->name }}
                                            @if(!$attendance->is_valid_location)
                                                <div class="text-xs text-red-600">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    Diluar area ({{ number_format($attendance->distance_from_office, 0) }}m)
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attendance->status === 'success')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Berhasil
                                                    </span>
                                            @elseif($attendance->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times-circle mr-1"></i>
                                                        Gagal
                                                    </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Pending
                                                    </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-clock text-gray-400 text-5xl mb-4"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data presensi</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Karyawan belum melakukan presensi.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
