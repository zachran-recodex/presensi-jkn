<div class="flex flex-col flex-grow bg-white pt-5 pb-4 overflow-y-auto border-r border-gray-200">
    <!-- Logo -->
    <div class="flex items-center flex-shrink-0 px-4">
        <div class="flex items-center">
            <div class="w-12 h-12 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="logo">
            </div>
            <div class="ml-3">
                <h1 class="text-lg font-semibold text-gray-900">Sistem Presensi</h1>
                <p class="text-xs text-gray-500">PT. Jaka Kuasa Nusantara</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="mt-6 px-4">
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">
                        @if(Auth::user()->isAdmin())
                            Administrator
                        @else
                            {{ Auth::user()->employee->employee_id ?? 'Karyawan' }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ Auth::user()->username }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-8 flex-1 px-2 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
            <i class="fas fa-tachometer-alt mr-3 text-lg {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
            Dashboard
        </a>

        @if(Auth::user()->isEmployee())
            <!-- Employee Navigation -->
            <div class="mt-6">
                <div class="px-2 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Presensi
                    </h3>
                </div>

                <a href="{{ route('attendance.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('attendance.index') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-camera mr-3 {{ request()->routeIs('attendance.index') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Presensi Sekarang
                </a>

                <a href="{{ route('attendance.history') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('attendance.history') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-history mr-3 {{ request()->routeIs('attendance.history') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Riwayat Presensi
                </a>

                @if(Auth::user()->employee)
                    <a href="{{ route('reports.employee', Auth::user()->employee) }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.employee') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                        <i class="fas fa-chart-bar mr-3 {{ request()->routeIs('reports.employee') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Laporan Saya
                    </a>
                @endif
            </div>
        @endif

        @if(Auth::user()->isAdmin())
            <!-- Admin Navigation -->
            <div class="mt-6">
                <div class="px-2 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Manajemen Presensi
                    </h3>
                </div>

                <a href="{{ route('admin.attendance.history') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-clock mr-3 {{ request()->routeIs('admin.attendance.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Data Presensi
                </a>

                <a href="{{ route('reports.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-chart-line mr-3 {{ request()->routeIs('reports.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Laporan & Analitik
                </a>
            </div>

            <div class="mt-6">
                <div class="px-2 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Manajemen Karyawan
                    </h3>
                </div>

                <a href="{{ route('employees.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employees.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-users mr-3 {{ request()->routeIs('employees.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Data Karyawan
                </a>

                <a href="{{ route('face-enrollment.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('face-enrollment.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-user-check mr-3 {{ request()->routeIs('face-enrollment.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Face Enrollment
                </a>
            </div>

            <div class="mt-6">
                <div class="px-2 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Pengaturan Sistem
                    </h3>
                </div>

                <a href="{{ route('locations.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('locations.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-map-marker-alt mr-3 {{ request()->routeIs('locations.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Lokasi Kantor
                </a>

                <!--
                <a href="{{ route('face-api-test.index') }}"
                class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('face-api-test.index') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors duration-200">
                    <i class="fas fa-vials mr-3 {{ request()->routeIs('face-api-test.index') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Face API Test
                </a>
                -->
            </div>
        @endif
    </nav>

    @php use Illuminate\Support\Facades\Auth; @endphp

    <!-- Quick Status (for employees) -->
    @if(Auth::user()->isEmployee() && Auth::user()->employee)
        <div class="px-4 py-4 border-t border-gray-200">
            <div class="bg-gray-50 rounded-lg p-3">
                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Status Hari Ini</h4>

                @php
                    $todayClockIn = Auth::user()->getTodayClockIn();
                    $todayClockOut = Auth::user()->getTodayClockOut();
                @endphp

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Masuk:</span>
                        <span class="font-medium {{ $todayClockIn ? ($todayClockIn->is_late ? 'text-red-600' : 'text-green-600') : 'text-gray-400' }}">
                            {{ $todayClockIn ? $todayClockIn->attendance_time->format('H:i') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Keluar:</span>
                        <span class="font-medium {{ $todayClockOut ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $todayClockOut ? $todayClockOut->attendance_time->format('H:i') : '-' }}
                        </span>
                    </div>

                    @if($todayClockIn && $todayClockIn->is_late)
                        <div class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Terlambat {{ $todayClockIn->late_minutes }} menit
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Logout -->
    <div class="px-4 pb-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                <i class="fas fa-sign-out-alt mr-3 text-red-500 group-hover:text-red-600"></i>
                Keluar
            </button>
        </form>
    </div>
</div>
