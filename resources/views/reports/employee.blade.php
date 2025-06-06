<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Employee Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-800 text-2xl font-bold">{{ substr($employee->user->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $employee->user->name }}</h3>
                            <div class="mt-1 flex items-center">
                                <span class="text-sm text-gray-600 mr-4">
                                    <i class="fas fa-id-badge mr-1"></i> {{ $employee->employee_id }}
                                </span>
                                <span class="text-sm text-gray-600 mr-4">
                                    <i class="fas fa-envelope mr-1"></i> {{ $employee->user->email }}
                                </span>
                                <span class="text-sm text-gray-600 mr-4">
                                    <i class="fas fa-phone mr-1"></i> {{ $employee->phone }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center">
                                <span class="text-sm text-gray-600 mr-4">
                                    <i class="fas fa-briefcase mr-1"></i> {{ $employee->position }}
                                </span>
                                <span class="text-sm text-gray-600 mr-4">
                                    <i class="fas fa-building mr-1"></i> {{ $employee->department }}
                                </span>
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $employee->location->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('reports.employee', $employee) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$startDate->format('Y-m-d')" />
                            </div>

                            <!-- End Date -->
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Akhir')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$endDate->format('Y-m-d')" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-day text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Hari</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Present Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Hari Hadir</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['present_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Hari Terlambat</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['late_days'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Work Hours -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-business-time text-indigo-400 text-lg"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Jam Kerja</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_work_hours'], 2) }} jam</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Kehadiran</h3>
                    <div class="space-y-6">
                        @forelse($attendances as $date => $dayAttendances)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-md font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                        </h4>
                                        @php
                                            $clockIn = $dayAttendances->where('type', 'clock_in')->where('status', 'success')->first();
                                            $clockOut = $dayAttendances->where('type', 'clock_out')->where('status', 'success')->first();
                                            $isLate = $clockIn && $clockIn->is_late;
                                        @endphp
                                        <div>
                                            @if($clockIn)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isLate ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $isLate ? 'Terlambat' : 'Tepat Waktu' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Tidak Hadir
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Clock In -->
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sign-in-alt text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">Jam Masuk</div>
                                                @if($clockIn)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $clockIn->attendance_time->format('H:i') }}
                                                        @if($isLate)
                                                            <span class="text-red-600 ml-2">(Terlambat {{ $clockIn->late_minutes }} menit)</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $clockIn->location->name }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">-</div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Clock Out -->
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sign-out-alt text-green-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">Jam Keluar</div>
                                                @if($clockOut)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $clockOut->attendance_time->format('H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $clockOut->location->name }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">-</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($clockIn && $clockOut)
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <div class="text-sm text-gray-600">
                                                    <i class="fas fa-business-time mr-1"></i> Durasi Kerja:
                                                    <span class="font-medium">
                                                        {{ $clockIn->attendance_time->diffInHours($clockOut->attendance_time) }} jam
                                                        {{ $clockIn->attendance_time->copy()->addHours($clockIn->attendance_time->diffInHours($clockOut->attendance_time))->diffInMinutes($clockOut->attendance_time) }} menit
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                Tidak ada data kehadiran untuk periode yang dipilih
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>