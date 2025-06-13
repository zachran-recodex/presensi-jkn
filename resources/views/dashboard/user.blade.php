<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Employee Info Card -->
            @if($employee)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Selamat datang, {{ $employee->user->name }}!</h3>
                                <p class="text-sm text-gray-600">{{ $employee->position }} - {{ $employee->department }}</p>
                                <p class="text-sm text-gray-500">{{ $employee->location->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">{{ now()->format('l, d F Y') }}</p>
                                <p class="text-lg font-semibold text-gray-900">{{ now()->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Attendance Action -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Presensi Hari Ini</h3>

                        @if($employee && $employee->status === 'active' && $employee->user->is_active)
                            @if(!$employee->user->hasFaceEnrolled())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                                        <div>
                                            <p class="text-yellow-800 font-medium">Wajah Belum Terdaftar</p>
                                            <p class="text-yellow-700 text-sm mt-1">Silakan hubungi admin untuk melakukan enrollment wajah sebelum dapat melakukan presensi.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Today's Status -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 {{ $todayClockIn ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                    <div class="flex items-center justify-center mb-2">
                                        @if($todayClockIn)
                                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                        @else
                                            <i class="fas fa-clock text-gray-400 text-xl"></i>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium {{ $todayClockIn ? 'text-green-900' : 'text-gray-700' }}">Clock In</p>
                                    <p class="text-xs {{ $todayClockIn ? 'text-green-700' : 'text-gray-500' }}">
                                        @if($todayClockIn)
                                            {{ $todayClockIn->attendance_time->format('H:i') }}
                                            @if($todayClockIn->is_late)
                                                <br><span class="text-red-600">Terlambat</span>
                                            @endif
                                        @else
                                            Belum Clock In
                                        @endif
                                    </p>
                                </div>

                                <div class="text-center p-3 {{ $todayClockOut ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                                    <div class="flex items-center justify-center mb-2">
                                        @if($todayClockOut)
                                            <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                        @else
                                            <i class="fas fa-sign-out-alt text-gray-400 text-xl"></i>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium {{ $todayClockOut ? 'text-blue-900' : 'text-gray-700' }}">Clock Out</p>
                                    <p class="text-xs {{ $todayClockOut ? 'text-blue-700' : 'text-gray-500' }}">
                                        @if($todayClockOut)
                                            {{ $todayClockOut->attendance_time->format('H:i') }}
                                        @else
                                            Belum Clock Out
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="text-center">
                                @if($canClockIn || $canClockOut)
                                    <a href="{{ route('attendance.index') }}"
                                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white {{ $canClockIn ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $canClockIn ? 'focus:ring-green-500' : 'focus:ring-red-500' }}">
                                        @if($canClockIn)
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            Clock In Sekarang
                                        @else
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Clock Out Sekarang
                                        @endif
                                    </a>
                                @else
                                    <div class="text-gray-500">
                                        <i class="fas fa-check-circle text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm">Presensi hari ini sudah selesai</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-red-800 font-medium">Tidak Dapat Mengakses Presensi</p>
                                        <p class="text-red-700 text-sm mt-1">
                                            @if(!$employee)
                                                Profil karyawan tidak ditemukan. Hubungi admin.
                                            @elseif($employee->status !== 'active')
                                                Akun karyawan tidak aktif. Status: {{ ucfirst($employee->status) }}
                                            @elseif(!$employee->user->is_active)
                                                Akun pengguna tidak aktif. Hubungi admin.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Work Schedule -->
                @if($employee)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Kerja</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Jam Masuk:</span>
                                    <span class="text-sm font-medium">{{ $workSchedule['start_time'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Jam Pulang:</span>
                                    <span class="text-sm font-medium">{{ $workSchedule['end_time'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Fleksibel:</span>
                                    <span class="text-sm font-medium {{ $workSchedule['is_flexible'] ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $workSchedule['is_flexible'] ? 'Ya' : 'Tidak' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lokasi:</span>
                                    <span class="text-sm font-medium">{{ $workSchedule['location'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Monthly Summary -->
            @if($employee)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Bulan Ini</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600">{{ $monthlySummary['present_days'] }}</p>
                                <p class="text-sm text-blue-800">Hari Hadir</p>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <p class="text-2xl font-bold text-red-600">{{ $monthlySummary['late_days'] }}</p>
                                <p class="text-sm text-red-800">Hari Terlambat</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600">{{ number_format($monthlySummary['total_work_hours'], 1) }}</p>
                                <p class="text-sm text-green-800">Total Jam Kerja</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <p class="text-2xl font-bold text-purple-600">
                                    @if($monthlySummary['present_days'] > 0)
                                        {{ number_format((($monthlySummary['present_days'] - $monthlySummary['late_days']) / $monthlySummary['present_days']) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </p>
                                <p class="text-sm text-purple-800">Kedisiplinan</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Attendance -->
            @if($employee && $recentAttendances->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Presensi 7 Hari Terakhir</h3>
                            <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($recentAttendances->take(5) as $date => $dayAttendances)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($date)->format('l') }}</p>
                                    </div>
                                    <div class="flex space-x-4 text-sm">
                                        @php
                                            $clockIn = $dayAttendances->where('type', 'clock_in')->first();
                                            $clockOut = $dayAttendances->where('type', 'clock_out')->first();
                                        @endphp
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">Masuk</p>
                                            <p class="font-medium {{ $clockIn ? ($clockIn->is_late ? 'text-red-600' : 'text-green-600') : 'text-gray-400' }}">
                                                {{ $clockIn ? $clockIn->attendance_time->format('H:i') : '-' }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">Pulang</p>
                                            <p class="font-medium {{ $clockOut ? 'text-blue-600' : 'text-gray-400' }}">
                                                {{ $clockOut ? $clockOut->attendance_time->format('H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(!$employee)
        <!-- Alert for missing employee profile -->
        <div x-data="{ show: true }" x-show="show" class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg max-w-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Profil Karyawan Tidak Ditemukan</p>
                    <p class="text-sm text-red-700 mt-1">Hubungi admin untuk membuat profil karyawan.</p>
                    <button @click="show = false" class="mt-2 text-xs text-red-600 hover:text-red-800">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
