<!-- Sidebar Navigation -->
<div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">

    <!-- Logo Section -->
    <div class="flex items-center flex-shrink-0 px-4">
        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-clock text-white text-xl"></i>
        </div>
        <div class="ml-3">
            <h1 class="text-xl font-bold text-gray-900">Presensi</h1>
            <p class="text-xs text-gray-500">PT. Jaka Kuasa Nusantara</p>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="mt-6 px-4">
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ Auth::user()->isAdmin() ? 'Administrator' : 'Employee' }}
                    </p>
                    @if(Auth::user()->employee)
                        <p class="text-xs text-blue-600">{{ Auth::user()->employee->employee_id }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Status (for employees) -->
            @if(Auth::user()->isEmployee())
                <div class="mt-3 pt-3 border-t border-gray-200">
                    @if(Auth::user()->hasClockedInToday())
                        <div class="flex items-center text-xs text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Clocked In Today</span>
                        </div>
                    @else
                        <div class="flex items-center text-xs text-red-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>Not Clocked In</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-8 flex-1 px-2 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <i class="fas fa-tachometer-alt mr-3 text-lg {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
            Dashboard
        </a>

        @if(Auth::user()->isEmployee())
            <!-- Employee Navigation -->

            <!-- Attendance Section -->
            <div class="mt-6">
                <div class="px-3 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Attendance
                    </h3>
                </div>

                <!-- Clock In/Out -->
                <a href="{{ route('attendance.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('attendance.index') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-clock mr-3 {{ request()->routeIs('attendance.index') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Clock In/Out
                    @if(!Auth::user()->hasClockedInToday())
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            !
                        </span>
                    @elseif(!Auth::user()->hasClockedOutToday())
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @endif
                </a>

                <!-- Attendance History -->
                <a href="{{ route('attendance.history') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('attendance.history') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-history mr-3 {{ request()->routeIs('attendance.history') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    My History
                </a>
            </div>

            <!-- Reports Section -->
            <div class="mt-6">
                <div class="px-3 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Reports
                    </h3>
                </div>

                <!-- My Report -->
                <a href="{{ route('reports.employee', Auth::user()->employee) }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('reports.employee') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-line mr-3 {{ request()->routeIs('reports.employee') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    My Report
                </a>
            </div>

        @else
            <!-- Admin Navigation -->

            <!-- Attendance Management -->
            <div class="mt-6">
                <div class="px-3 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Attendance Management
                    </h3>
                </div>

                <!-- Attendance Overview -->
                <a href="{{ route('admin.attendance.history') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-clock mr-3 {{ request()->routeIs('admin.attendance.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Attendance History
                </a>

                <!-- Reports -->
                <a href="{{ route('reports.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('reports.*') && !request()->routeIs('reports.employee') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-bar mr-3 {{ request()->routeIs('reports.*') && !request()->routeIs('reports.employee') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Reports & Analytics
                </a>
            </div>

            <!-- Employee Management -->
            <div class="mt-6">
                <div class="px-3 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Employee Management
                    </h3>
                </div>

                <!-- Manage Employees -->
                <a href="{{ route('employees.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('employees.*') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users mr-3 {{ request()->routeIs('employees.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Manage Employees
                    <span class="ml-auto text-xs text-gray-400">{{ \App\Models\Employee::active()->count() }}</span>
                </a>

                <!-- Face Enrollment -->
                <a href="{{ route('face-enrollment.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('face-enrollment.*') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-user-check mr-3 {{ request()->routeIs('face-enrollment.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Face Enrollment
                    @php
                        $totalEmployees = \App\Models\Employee::active()->count();
                        $enrolledCount = \App\Models\User::whereNotNull('face_id')->whereHas('employee', function($q) { $q->where('status', 'active'); })->count();
                        $pendingCount = $totalEmployees - $enrolledCount;
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- System Management -->
            <div class="mt-6">
                <div class="px-3 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        System Management
                    </h3>
                </div>

                <!-- Office Locations -->
                <a href="{{ route('locations.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('locations.*') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-map-marker-alt mr-3 {{ request()->routeIs('locations.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Office Locations
                    <span class="ml-auto text-xs text-gray-400">{{ \App\Models\Location::active()->count() }}</span>
                </a>
            </div>
        @endif

        <!-- Profile Section -->
        <div class="mt-6">
            <div class="px-3 py-2">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Account
                </h3>
            </div>

            <!-- Profile Settings -->
            <a href="{{ route('profile.edit') }}"
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-user-cog mr-3 {{ request()->routeIs('profile.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                Profile Settings
            </a>
        </div>
    </nav>

    <!-- Quick Actions (at bottom) -->
    <div class="flex-shrink-0 px-2 py-4 space-y-1">

        @if(Auth::user()->isEmployee())
            <!-- Quick Clock In/Out Button -->
            @if(!Auth::user()->hasClockedInToday())
                <a href="{{ route('attendance.index') }}"
                   class="group flex items-center justify-center px-4 py-3 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-clock mr-2"></i>
                    Clock In Now
                </a>
            @elseif(!Auth::user()->hasClockedOutToday())
                <a href="{{ route('attendance.index') }}"
                   class="group flex items-center justify-center px-4 py-3 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Clock Out Now
                </a>
            @else
                <div class="flex items-center justify-center px-4 py-3 text-sm font-medium rounded-md text-green-700 bg-green-100">
                    <i class="fas fa-check mr-2"></i>
                    Day Complete
                </div>
            @endif
        @else
            <!-- Admin Quick Actions -->
            <button onclick="exportTodayReport()"
                   class="group flex items-center justify-center w-full px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Export Today
            </button>

            <a href="{{ route('employees.create') }}"
               class="group flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-user-plus mr-2"></i>
                Add Employee
            </a>
        @endif

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit"
                   class="group flex items-center justify-center w-full px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
            </button>
        </form>
    </div>

    <!-- Footer Info -->
    <div class="flex-shrink-0 px-4 py-2 border-t border-gray-200">
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>v1.0.0</span>
            <span>{{ date('Y') }}</span>
        </div>
    </div>
</div>

<!-- Sidebar-specific JavaScript -->
<script>
    // Export today's report function (for admin)
    function exportTodayReport() {
        const today = new Date().toISOString().split('T')[0];
        window.open(`{{ route('reports.export.daily') }}?date=${today}`, '_blank');
    }

    // Auto-collapse sidebar on mobile after navigation
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('nav a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Close mobile sidebar after navigation
                if (window.innerWidth < 1024) { // lg breakpoint
                    Alpine.store('sidebar', { open: false });
                }
            });
        });
    });
</script>
