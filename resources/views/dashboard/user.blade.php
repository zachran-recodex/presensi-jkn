<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-home mr-2"></i>
            Dashboard Presensi
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                    <p class="text-blue-100 mt-1">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
                <div class="hidden sm:block">
                    <i class="fas fa-user-shield text-4xl text-blue-200"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Clock In/Out Card -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock mr-2 text-blue-600"></i>
                    Presensi Hari Ini
                </h4>

                <!-- Current Time -->
                <div class="text-center mb-6 p-4 bg-gray-50 rounded-lg">
                    <div id="current-time" class="text-3xl font-bold text-gray-800 mb-1"></div>
                    <div class="text-gray-600">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
                </div>

                <!-- Attendance Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <!-- Clock In Status -->
                    <div class="text-center p-4 rounded-lg {{ $hasClockIn ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                        <i class="fas fa-sign-in-alt text-2xl {{ $hasClockIn ? 'text-green-600' : 'text-gray-400' }} mb-2"></i>
                        <div class="font-medium {{ $hasClockIn ? 'text-green-800' : 'text-gray-600' }}">Clock In</div>
                        @if($hasClockIn)
                            <div class="text-sm text-green-600 mt-1">
                                {{ $todayAttendance->where('type', 'in')->first()->created_at->format('H:i:s') }}
                            </div>
                        @else
                            <div class="text-sm text-gray-500 mt-1">Belum masuk</div>
                        @endif
                    </div>

                    <!-- Clock Out Status -->
                    <div class="text-center p-4 rounded-lg {{ $hasClockOut ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }}">
                        <i class="fas fa-sign-out-alt text-2xl {{ $hasClockOut ? 'text-blue-600' : 'text-gray-400' }} mb-2"></i>
                        <div class="font-medium {{ $hasClockOut ? 'text-blue-800' : 'text-gray-600' }}">Clock Out</div>
                        @if($hasClockOut)
                            <div class="text-sm text-blue-600 mt-1">
                                {{ $todayAttendance->where('type', 'out')->first()->created_at->format('H:i:s') }}
                            </div>
                        @else
                            <div class="text-sm text-gray-500 mt-1">Belum keluar</div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    @if(!$hasClockIn)
                        <a href="{{ route('attendance.index') }}"
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Clock In Sekarang
                        </a>
                    @elseif(!$hasClockOut)
                        <a href="{{ route('attendance.index') }}"
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition text-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Clock Out Sekarang
                        </a>
                    @else
                        <div class="flex-1 bg-gray-100 text-gray-600 font-medium py-3 px-4 rounded-lg text-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Presensi Hari Ini Selesai
                        </div>
                    @endif

                    <a href="{{ route('attendance.history') }}"
                       class="flex-1 sm:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg transition text-center">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-4">
                <!-- Monthly Stats -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                        Statistik Bulan Ini
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Presensi</span>
                            <span class="font-bold text-blue-600">{{ $stats['total_attendance_this_month'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tepat Waktu</span>
                            <span class="font-bold text-green-600">{{ $stats['on_time_this_month'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Terlambat</span>
                            <span class="font-bold text-red-600">{{ $stats['late_this_month'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Face Recognition Status -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user-check mr-2 text-blue-600"></i>
                        Status Wajah
                    </h4>
                    @if(auth()->user()->hasFaceEnrolled())
                        <div class="text-center p-4 bg-green-50 border border-green-200 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <div class="font-medium text-green-800">Wajah Terdaftar</div>
                            <div class="text-sm text-green-600 mt-1">Siap untuk presensi</div>
                        </div>
                    @else
                        <div class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mb-2"></i>
                            <div class="font-medium text-yellow-800">Wajah Belum Terdaftar</div>
                            <div class="text-sm text-yellow-600 mt-1">Hubungi admin untuk registrasi wajah</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Attendance History -->
        @if($recentAttendance->isNotEmpty())
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    Riwayat Presensi (7 Hari Terakhir)
                </h4>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentAttendance as $date => $attendances)
                            @php
                                $clockIn = $attendances->where('type', 'in')->first();
                                $clockOut = $attendances->where('type', 'out')->first();
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMM YYYY') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($clockIn)
                                        <span class="text-green-600 font-medium">{{ $clockIn->created_at->format('H:i:s') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($clockOut)
                                        <span class="text-blue-600 font-medium">{{ $clockOut->created_at->format('H:i:s') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($clockIn && $clockOut)
                                        @if($clockIn->created_at->format('H:i:s') <= '08:00:00')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Tepat Waktu
                                        </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Terlambat
                                        </span>
                                        @endif
                                    @elseif($clockIn)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        Belum Clock Out
                                    </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>
                                        Tidak Hadir
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Update current time every second
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }

        // Update time immediately and then every second
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</x-app-layout>
