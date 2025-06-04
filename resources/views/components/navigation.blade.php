<!-- Top Navigation Bar -->
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true"
                       class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-200">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Logo & Title (visible on mobile when sidebar is closed) -->
                <div class="lg:hidden ml-4 flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold text-gray-900">Presensi</h1>
                    </div>
                </div>

                <!-- Breadcrumb (desktop only) -->
                <div class="hidden lg:flex items-center space-x-4">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <div>
                                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                                        <i class="fas fa-home"></i>
                                        <span class="sr-only">Home</span>
                                    </a>
                                </div>
                            </li>
                            @if(!request()->routeIs('dashboard'))
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-300 text-sm"></i>
                                        <span class="ml-2 text-sm font-medium text-gray-500">
                                            @yield('breadcrumb', ucfirst(request()->segment(1)))
                                        </span>
                                    </div>
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">

                <!-- Current time -->
                <div class="hidden sm:flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-2 text-gray-400"></i>
                    <span id="current-time">{{ now()->format('H:i:s') }}</span>
                </div>

                <!-- Quick attendance button (for employees only) -->
                @if(auth()->user()->isEmployee())
                    <div class="hidden sm:block">
                        @if(!auth()->user()->hasClockedInToday())
                            <a href="{{ route('attendance.index') }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <i class="fas fa-clock mr-2"></i>
                                Clock In
                            </a>
                        @elseif(!auth()->user()->hasClockedOutToday())
                            <a href="{{ route('attendance.index') }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Clock Out
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-md">
                                <i class="fas fa-check mr-2"></i>
                                Complete
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Notifications dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                           class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                           aria-label="View notifications">
                        <i class="fas fa-bell text-lg"></i>
                        <!-- Notification badge -->
                        @if(auth()->user()->isAdmin())
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                        @endif
                    </button>

                    <!-- Notification dropdown -->
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @if(auth()->user()->isAdmin())
                                    <!-- Sample admin notifications -->
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-gray-800">5 employees haven't clocked in today</p>
                                                <p class="text-xs text-gray-500">{{ now()->format('H:i') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user-plus text-green-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-gray-800">New employee added successfully</p>
                                                <p class="text-xs text-gray-500">2 hours ago</p>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <!-- Sample employee notifications -->
                                    <div class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                        <p class="text-sm">No new notifications</p>
                                    </div>
                                @endif
                            </div>
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-200">
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-50 text-center transition-colors duration-200">
                                        View all notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                           class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                           aria-label="User menu">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <div class="ml-3 hidden lg:block text-left">
                            <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">
                                {{ Auth::user()->isAdmin() ? 'Administrator' : 'Employee' }}
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs hidden lg:block"></i>
                    </button>

                    <!-- Profile dropdown menu -->
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <!-- User info header -->
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                @if(Auth::user()->employee)
                                    <p class="text-xs text-gray-500">{{ Auth::user()->employee->employee_id }}</p>
                                @endif
                            </div>

                            <!-- Profile link -->
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                Edit Profile
                            </a>

                            <!-- Employee-specific links -->
                            @if(auth()->user()->isEmployee())
                                <a href="{{ route('attendance.history') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-history mr-3 text-gray-400"></i>
                                    My Attendance History
                                </a>

                                <a href="{{ route('reports.employee', auth()->user()->employee) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-chart-line mr-3 text-gray-400"></i>
                                    My Report
                                </a>
                            @endif

                            <!-- Admin-specific links -->
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-200 mt-1 pt-1">
                                    <a href="{{ route('employees.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <i class="fas fa-users mr-3 text-gray-400"></i>
                                        Manage Employees
                                    </a>
                                    <a href="{{ route('reports.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <i class="fas fa-chart-bar mr-3 text-gray-400"></i>
                                        Reports Dashboard
                                    </a>
                                </div>
                            @endif

                            <!-- Logout -->
                            <div class="border-t border-gray-200 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                           class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Page Status Bar (for employee attendance status) -->
@if(auth()->user()->isEmployee())
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Today's Status:</span>

                        @if(auth()->user()->hasClockedInToday())
                            @php
                                $clockIn = auth()->user()->getTodayClockIn();
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Clocked In: {{ $clockIn->attendance_time->format('H:i') }}
                                @if($clockIn->is_late)
                                    <span class="ml-1 text-red-600">({{ $clockIn->late_minutes }}m late)</span>
                                @endif
                            </span>

                            @if(auth()->user()->hasClockedOutToday())
                                @php
                                    $clockOut = auth()->user()->getTodayClockOut();
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-sign-out-alt mr-1"></i>
                                    Clocked Out: {{ $clockOut->attendance_time->format('H:i') }}
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Not Clocked In Yet
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center text-gray-500">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <span>{{ auth()->user()->employee->location->name ?? 'No location assigned' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Real-time clock script -->
<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toTimeString().split(' ')[0]; // HH:MM:SS format
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Update clock every second
    setInterval(updateClock, 1000);

    // Update immediately
    updateClock();
</script>
