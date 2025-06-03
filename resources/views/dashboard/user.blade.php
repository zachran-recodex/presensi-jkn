<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-user-clock mr-2 text-blue-600"></i>
                    Dashboard Karyawan
                </h2>
                <p class="text-sm text-gray-600 mt-1">Selamat datang, {{ $employee->user->name }}!</p>
            </div>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Today's Status -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg text-white">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Status Presensi Hari Ini</h3>
                        <div class="space-y-2">
                            @if($todayClockIn)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-sign-in-alt text-green-300"></i>
                                    <span class="text-sm">
                                        Masuk: {{ $todayClockIn->attendance_time->format('H:i') }}
                                        @if($todayClockIn->is_late)
                                            <span class="bg-red-500 px-2 py-1 rounded-full text-xs ml-2">
                                                Terlambat {{ $todayClockIn->late_minutes }} menit
                                            </span>
                                        @else
                                            <span class="bg-green-500 px-2 py-1 rounded-full text-xs ml-2">
                                                Tepat waktu
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-times-circle text-red-300"></i>
                                    <span class="text-sm">Belum melakukan clock in</span>
                                </div>
                            @endif

                            @if($todayClockOut)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-sign-out-alt text-blue-300"></i>
                                    <span class="text-sm">Pulang: {{ $todayClockOut->attendance_time->format('H:i') }}</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-clock text-yellow-300"></i>
                                    <span class="text-sm">Belum melakukan clock out</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <i class="fas fa-user-circle text-4xl mb-2"></i>
                            <p class="text-sm">{{ $employee->employee_id }}</p>
                            <p class="text-xs opacity-80">{{ $employee->department }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Clock In Button -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @if($canClockIn)
                    <div class="text-center">
                        <div class="bg-green-100 text-green-600 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Clock In</h3>
                        <p class="text-sm text-gray-600 mb-4">Klik untuk melakukan presensi masuk</p>
                        <a href="{{ route('attendance.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-camera mr-2"></i>
                            Presensi Masuk
                        </a>
                    </div>
                @else
                    <div class="text-center">
                        <div class="bg-gray-100 text-gray-400 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Clock In</h3>
                        <p class="text-sm text-gray-600 mb-4">Anda sudah melakukan presensi masuk</p>
                        <button disabled
                                class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>
                            Sudah Clock In
                        </button>
                    </div>
                @endif
            </div>

            <!-- Clock Out Button -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @if($canClockOut)
                    <div class="text-center">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Clock Out</h3>
                        <p class="text-sm text-gray-600 mb-4">Klik untuk melakukan presensi pulang</p>
                        <a href="{{ route('attendance.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-camera mr-2"></i>
                            Presensi Pulang
                        </a>
                    </div>
                @else
                    <div class="text-center">
                        <div class="bg-gray-100 text-gray-400 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-{{ $todayClockOut ? 'check-circle' : 'times-circle' }} text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Clock Out</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            @if($todayClockOut)
                                Anda sudah melakukan presensi pulang
                            @else
                                Lakukan clock in terlebih dahulu
                            @endif
                        </p>
                        <button disabled
                                class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                            <i class="fas fa-{{ $todayClockOut ? 'check' : 'times' }} mr-2"></i>
                            {{ $todayClockOut ? 'Sudah Clock Out' : 'Tidak Tersedia' }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Summary & Work Schedule -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie mr-2 text-purple-600"></i>
                        Ringkasan Bulan Ini
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Hari Hadir</p>
                                    <p class="text-sm text-gray-600">Dari total hari kerja</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-green-600">{{ $monthlySummary['present_days'] }}</p>
                                <p class="text-sm text-gray-500">hari</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-yellow-100 text-yellow-600 p-2 rounded-lg">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Terlambat</p>
                                    <p class="text-sm text-gray-600">Jumlah hari terlambat</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-yellow-600">{{ $monthlySummary['late_days'] }}</p>
                                <p class="text-sm text-gray-500">hari</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                    <i class="fas fa-business-time"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Total Jam Kerja</p>
                                    <p class="text-sm text-gray-600">Akumulasi jam kerja</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-blue-600">{{ number_format($monthlySummary['total_work_hours'], 1) }}</p>
                                <p class="text-sm text-gray-500">jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Schedule Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                        Jadwal Kerja
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-sun text-orange-500"></i>
                                <span class="font-medium text-gray-900">Jam Masuk</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $workSchedule['start_time'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-moon text-indigo-500"></i>
                                <span class="font-medium text-gray-900">Jam Pulang</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $workSchedule['end_time'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                <span class="font-medium text-gray-900">Lokasi</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $workSchedule['location'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-{{ $workSchedule['is_flexible'] ? 'check-circle text-green-500' : 'times-circle text-red-500' }}"></i>
                                <span class="font-medium text-gray-900">Jam Fleksibel</span>
                            </div>
                            <span class="text-sm font-bold {{ $workSchedule['is_flexible'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $workSchedule['is_flexible'] ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-history mr-2 text-gray-600"></i>
                        History Presensi (7 Hari Terakhir)
                    </h3>
                    <a href="{{ route('attendance.history') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>

                @if($recentAttendances->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentAttendances as $date => $dayAttendances)
                            @php
                                $clockIn = $dayAttendances->where('type', 'clock_in')->first();
                                $clockOut = $dayAttendances->where('type', 'clock_out')->first();
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->format('d') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($date)->format('M') }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Masuk</p>
                                        <p class="font-medium {{ $clockIn && $clockIn->is_late ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $clockIn ? $clockIn->attendance_time->format('H:i') : '-' }}
                                        </p>
                                        @if($clockIn && $clockIn->is_late)
                                            <p class="text-xs text-red-600">+{{ $clockIn->late_minutes }}m</p>
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Pulang</p>
                                        <p class="font-medium text-blue-600">
                                            {{ $clockOut ? $clockOut->attendance_time->format('H:i') : '-' }}
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Status</p>
                                        @if($clockIn && $clockOut)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Lengkap
                                            </span>
                                        @elseif($clockIn)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Belum Pulang
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Tidak Hadir
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-3xl mb-2"></i>
                        <p>Belum ada history presensi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');

            // Update any time elements if they exist
            const timeElements = document.querySelectorAll('.live-time');
            timeElements.forEach(element => {
                element.textContent = timeString;
            });
        }

        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>
    @endpush
</x-app-layout>
