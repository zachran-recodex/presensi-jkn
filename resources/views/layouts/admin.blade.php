<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Presensi') }} - Admin - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false, showQuickStats: true }">
    <!-- Page Wrapper -->
    <div class="min-h-screen">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 flex z-40 lg:hidden"
             @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        </div>

        <!-- Admin Sidebar for mobile and desktop -->
        <div class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-blue-800 to-blue-900 pt-5 pb-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-shield text-blue-600 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold text-white">Admin Panel</h1>
                        <p class="text-xs text-blue-200">PT. Jaka Kuasa Nusantara</p>
                    </div>
                </div>

                <!-- Admin User Info -->
                <div class="mt-6 px-4">
                    <div class="bg-blue-700 bg-opacity-50 rounded-lg p-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-blue-200">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-8 flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                        <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
                        Dashboard
                    </a>

                    <!-- Attendance Management -->
                    <div class="space-y-1">
                        <div class="text-blue-200 text-xs font-semibold uppercase tracking-wider px-2 py-2">
                            Attendance Management
                        </div>

                        <a href="{{ route('admin.attendance.history') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                            <i class="fas fa-clock mr-3"></i>
                            Attendance History
                        </a>

                        <a href="{{ route('reports.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                            <i class="fas fa-chart-line mr-3"></i>
                            Reports & Analytics
                        </a>
                    </div>

                    <!-- Employee Management -->
                    <div class="space-y-1">
                        <div class="text-blue-200 text-xs font-semibold uppercase tracking-wider px-2 py-2 mt-6">
                            Employee Management
                        </div>

                        <a href="{{ route('employees.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('employees.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                            <i class="fas fa-users mr-3"></i>
                            Manage Employees
                        </a>

                        <a href="{{ route('face-enrollment.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('face-enrollment.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                            <i class="fas fa-user-check mr-3"></i>
                            Face Enrollment
                        </a>
                    </div>

                    <!-- System Management -->
                    <div class="space-y-1">
                        <div class="text-blue-200 text-xs font-semibold uppercase tracking-wider px-2 py-2 mt-6">
                            System Management
                        </div>

                        <a href="{{ route('locations.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('locations.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors duration-200">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            Office Locations
                        </a>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8 pt-4 border-t border-blue-700">
                        <div class="space-y-1">
                            <button onclick="exportTodayReport()"
                                   class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white transition-colors duration-200">
                                <i class="fas fa-download mr-3"></i>
                                Export Today's Report
                            </button>

                            <button onclick="refreshDashboard()"
                                   class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:text-white transition-colors duration-200">
                                <i class="fas fa-sync mr-3"></i>
                                Refresh Data
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Mobile admin sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-blue-800 to-blue-900 lg:hidden">
            <!-- Same content as desktop sidebar -->
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <!-- Mobile Close Button -->
                <div class="flex items-center justify-between px-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-shield text-blue-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-base font-semibold text-white">Admin Panel</h1>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="text-blue-200 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Mobile Navigation (abbreviated) -->
                <nav class="mt-6 flex-1 px-2 space-y-1">
                    <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="{{ route('employees.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700">
                        <i class="fas fa-users mr-3"></i>Employees
                    </a>
                    <a href="{{ route('reports.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700">
                        <i class="fas fa-chart-line mr-3"></i>Reports
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content area -->
        <div class="lg:pl-72 flex flex-col min-h-screen">
            <!-- Top navigation -->
            <div class="sticky top-0 z-10 bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-600">
                                <i class="fas fa-bars text-xl"></i>
                            </button>

                            <!-- Page title -->
                            <h1 class="ml-4 lg:ml-0 text-xl font-semibold text-gray-900">
                                @yield('header', 'Admin Dashboard')
                            </h1>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Quick stats toggle -->
                            <button @click="showQuickStats = !showQuickStats"
                                   class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md transition-colors duration-200"
                                   title="Toggle Quick Stats">
                                <i class="fas fa-chart-bar"></i>
                            </button>

                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md transition-colors duration-200">
                                    <i class="fas fa-bell"></i>
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                                </button>
                                <!-- Notification dropdown would go here -->
                            </div>

                            <!-- User dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user-circle mr-2"></i>Profile
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Bar (toggleable) -->
            <div x-show="showQuickStats"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-lg font-semibold" id="quick-stat-present">-</div>
                            <div class="text-xs opacity-90">Present Today</div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold" id="quick-stat-late">-</div>
                            <div class="text-xs opacity-90">Late Today</div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold" id="quick-stat-absent">-</div>
                            <div class="text-xs opacity-90">Absent Today</div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold" id="quick-stat-rate">-</div>
                            <div class="text-xs opacity-90">Attendance Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alert')

            <!-- Page Content -->
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            © {{ date('Y') }} PT. Jaka Kuasa Nusantara - Admin Panel
                        </p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>Last updated: <span id="last-refresh-time">{{ now()->format('H:i:s') }}</span></span>
                            <button onclick="refreshDashboard()" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Component -->
    @include('components.modal')

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Admin-specific JavaScript -->
    <script>
        // CSRF Token
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Auto-refresh dashboard stats
        function refreshDashboard() {
            fetch('{{ route("dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('quick-stat-present').textContent = data.today_present;
                    document.getElementById('quick-stat-late').textContent = data.today_late || 0;
                    document.getElementById('quick-stat-absent').textContent = data.today_absent;
                    document.getElementById('quick-stat-rate').textContent = data.attendance_rate + '%';
                    document.getElementById('last-refresh-time').textContent = new Date().toLocaleTimeString();
                })
                .catch(error => console.error('Error refreshing dashboard:', error));
        }

        // Export today's report
        function exportTodayReport() {
            const today = new Date().toISOString().split('T')[0];
            window.open(`{{ route('reports.export.daily') }}?date=${today}`, '_blank');
        }

        // Load initial stats
        document.addEventListener('DOMContentLoaded', function() {
            refreshDashboard();

            // Auto-refresh every 5 minutes
            setInterval(refreshDashboard, 300000);
        });

        // Global notification function
        window.showNotification = function(message, type = 'success') {
            const event = new CustomEvent('show-notification', {
                detail: { message, type }
            });
            window.dispatchEvent(event);
        };
    </script>
</body>
</html>
