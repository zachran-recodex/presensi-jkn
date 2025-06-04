<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold">Selamat Datang, {{ $employee->user->name }}!</h1>
                            <p class="text-blue-100 mt-1">{{ $employee->position }} - {{ $employee->department }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm opacity-90">{{ now()->format('l, d F Y') }}</div>
                            <div class="text-2xl font-bold" x-data="clock()" x-text="time"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Status & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Today's Attendance Status -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Presensi Hari Ini</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Clock In Status -->
                            <div class="border rounded-lg p-4 {{ $todayClockIn ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($todayClockIn)
                                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium {{ $todayClockIn ? 'text-green-800' : 'text-gray-600' }}">Clock In</p>
                                        @if($todayClockIn)
                                            <p class="text-lg font-semibold {{ $todayClockIn->is_late ? 'text-red-600' : 'text-green-900' }}">
                                                {{ $todayClockIn->attendance_time->format('H:i:s') }}
                                            </p>
                                            @if($todayClockIn->is_late)
                                                <p class="text-xs text-red-600">Terlambat {{ $todayClockIn->late_minutes }} menit</p>
                                            @else
                                                <p class="text-xs text-green-600">Tepat waktu</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500">Belum clock in</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Clock Out Status -->
                            <div class="border rounded-lg p-4 {{ $todayClockOut ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($todayClockOut)
                                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium {{ $todayClockOut ? 'text-green-800' : 'text-gray-600' }}">Clock Out</p>
                                        @if($todayClockOut)
                                            <p class="text-lg font-semibold text-green-900">
                                                {{ $todayClockOut->attendance_time->format('H:i:s') }}
                                            </p>
                                            @if($todayClockIn && $todayClockOut)
                                                @php
                                                    $workDuration = $todayClockIn->attendance_time->diffInHours($todayClockOut->attendance_time, true);
                                                @endphp
                                                <p class="text-xs text-green-600">Durasi: {{ number_format($workDuration, 1) }} jam</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500">Belum clock out</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Action Button -->
                        <div class="mt-6">
                            @if($canClockIn)
                                <a href="{{ route('attendance.index') }}"
                                   class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 flex items-center justify-center space-x-2 font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Clock In Sekarang</span>
                                </a>
                            @elseif($canClockOut)
                                <a href="{{ route('attendance.index') }}"
                                   class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 flex items-center justify-center space-x-2 font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Clock Out Sekarang</span>
                                </a>
                            @else
                                <div class="w-full bg-gray-100 text-gray-600 px-4 py-3 rounded-lg flex items-center justify-center space-x-2 font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Presensi Hari Ini Selesai</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Work Schedule Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Kerja</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Jam Masuk</span>
                                <span class="font-medium">{{ $workSchedule['start_time'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Jam Pulang</span>
                                <span class="font-medium">{{ $workSchedule['end_time'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Jenis Jam Kerja</span>
                                <span class="text-sm {{ $workSchedule['is_flexible'] ? 'text-green-600' : 'text-blue-600' }}">
                                    {{ $workSchedule['is_flexible'] ? 'Fleksibel' : 'Tetap' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Lokasi</span>
                                <span class="text-sm font-medium">{{ $workSchedule['location'] }}</span>
                            </div>
                        </div>

                        <!-- Face Enrollment Status -->
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status Wajah</span>
                                @if($employee->user->hasFaceEnrolled())
                                    <span class="text-sm text-green-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Terdaftar
                                    </span>
                                @else
                                    <span class="text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Belum Terdaftar
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Bulan Ini</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $monthlySummary['present_days'] }}</div>
                            <div class="text-sm text-gray-600">Hari Hadir</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $monthlySummary['late_days'] }}</div>
                            <div class="text-sm text-gray-600">Hari Terlambat</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($monthlySummary['total_work_hours'], 1) }}</div>
                            <div class="text-sm text-gray-600">Total Jam Kerja</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                @if($monthlySummary['present_days'] > 0)
                                    {{ number_format((($monthlySummary['present_days'] - $monthlySummary['late_days']) / $monthlySummary['present_days']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </div>
                            <div class="text-sm text-gray-600">Kedisiplinan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Presensi (7 Hari Terakhir)</h3>
                        <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Semua
                        </a>
                    </div>

                    @if($recentAttendances->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAttendances as $date => $dayAttendances)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                                        </h4>
                                        <div class="text-sm text-gray-500">
                                            {{ $dayAttendances->count() }} record{{ $dayAttendances->count() > 1 ? 's' : '' }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach(['clock_in', 'clock_out'] as $type)
                                            @php
                                                $attendance = $dayAttendances->where('type', $type)->where('status', 'success')->first();
                                            @endphp
                                            <div class="flex items-center space-x-3">
                                                <div class="w-3 h-3 rounded-full {{ $attendance ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium">
                                                        {{ $type === 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                                    </div>
                                                    @if($attendance)
                                                        <div class="text-sm text-gray-600">
                                                            {{ $attendance->attendance_time->format('H:i:s') }}
                                                            @if($attendance->is_late && $type === 'clock_in')
                                                                <span class="text-red-600 ml-1">(+{{ $attendance->late_minutes }}m)</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-400">-</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat presensi</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai melakukan presensi untuk melihat riwayat.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('attendance.index') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Presensi</span>
                        </a>

                        <a href="{{ route('attendance.history') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Riwayat</span>
                        </a>

                        @if($employee)
                            <a href="{{ route('reports.employee', $employee) }}"
                               class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H9a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Laporan</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}"
                           class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Profil</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function clock() {
            return {
                time: '',
                init() {
                    this.updateTime();
                    setInterval(() => {
                        this.updateTime();
                    }, 1000);
                },
                updateTime() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
