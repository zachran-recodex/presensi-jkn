<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Karyawan') }}
        </h2>
    </x-slot>

    @section('header-actions')
        <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Employee Info Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-2xl font-bold text-blue-800">{{ substr($employee->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">{{ $employee->user->name }}</h3>
                                    <p class="text-gray-600">{{ $employee->position }}</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $employee->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $employee->status === 'terminated' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Informasi Akun</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-gray-600 text-sm">Username:</span>
                                            <p class="font-medium">{{ $employee->user->username }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Email:</span>
                                            <p class="font-medium">{{ $employee->user->email }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Status Akun:</span>
                                            <p class="font-medium {{ $employee->user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $employee->user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Informasi Karyawan</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-gray-600 text-sm">ID Karyawan:</span>
                                            <p class="font-medium">{{ $employee->employee_id }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Departemen:</span>
                                            <p class="font-medium">{{ $employee->department ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Telepon:</span>
                                            <p class="font-medium">{{ $employee->phone ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Lokasi & Jadwal</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-gray-600 text-sm">Lokasi Kantor:</span>
                                            <p class="font-medium">{{ $employee->location->name }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Tanggal Bergabung:</span>
                                            <p class="font-medium">{{ $employee->join_date->format('d F Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 text-sm">Jam Kerja:</span>
                                            <p class="font-medium">
                                                {{ $employee->work_start_time->format('H:i') }} - {{ $employee->work_end_time->format('H:i') }}
                                                @if($employee->is_flexible_time)
                                                    <span class="text-green-600 text-xs ml-2">(Fleksibel)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if($employee->notes)
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Catatan</h4>
                                    <div class="p-3 bg-gray-50 rounded-md">
                                        {{ $employee->notes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary Card -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Presensi Bulan Ini</h3>

                            <div class="space-y-4">
                                <div class="bg-blue-50 p-3 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-800">Total Hari Kerja</span>
                                        <span class="font-bold text-blue-800">{{ $monthlySummary['total_days'] }} hari</span>
                                    </div>
                                </div>

                                <div class="bg-green-50 p-3 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-green-800">Hari Hadir</span>
                                        <span class="font-bold text-green-800">{{ $monthlySummary['present_days'] }} hari</span>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 p-3 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-yellow-800">Hari Terlambat</span>
                                        <span class="font-bold text-yellow-800">{{ $monthlySummary['late_days'] }} hari</span>
                                    </div>
                                </div>

                                <div class="bg-purple-50 p-3 rounded-md">
                                    <div class="flex justify-between items-center">
                                        <span class="text-purple-800">Total Jam Kerja</span>
                                        <span class="font-bold text-purple-800">{{ $monthlySummary['total_work_hours'] }} jam</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('reports.employee', $employee) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-chart-bar mr-1"></i> Lihat Laporan Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Presensi Terbaru</h3>

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
                                @forelse($employee->attendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->attendance_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $attendance->type === 'clock_in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}
                                            ">
                                                {{ $attendance->type === 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->attendance_time->format('H:i:s') }}
                                            @if($attendance->type === 'clock_in' && $attendance->is_late)
                                                <span class="text-xs text-red-600 ml-1">(Terlambat {{ $attendance->late_minutes }} menit)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $attendance->location->name }}
                                            @if(!$attendance->is_valid_location)
                                                <span class="text-xs text-red-600 ml-1">(Diluar area)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $attendance->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $attendance->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $attendance->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data presensi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.attendance.history', ['employee_id' => $employee->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-history mr-1"></i> Lihat Semua Riwayat Presensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
