<div class="sticky top-0 z-10 bg-white shadow-sm border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true"
                        class="lg:hidden -ml-2 mr-2 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-200">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumb / Current Page -->
                <div class="hidden sm:flex items-center">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors duration-200">
                            <i class="fas fa-home"></i>
                        </a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-900 font-medium">
                            @yield('breadcrumb', 'Dashboard')
                        </span>
                    </div>
                </div>

                <!-- Current Time (Live) -->
                <div class="ml-4 hidden md:flex items-center">
                    <div class="text-sm text-gray-500">
                        <span id="current-time" class="font-mono"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Quick Stats for Admin -->
                @if(Auth::user()->isAdmin())
                    <div class="hidden xl:flex items-center space-x-6 text-sm">
                        <div class="text-center">
                            <div id="quick-present" class="font-semibold text-green-600">-</div>
                            <div class="text-xs text-gray-500">Hadir</div>
                        </div>
                        <div class="text-center">
                            <div id="quick-late" class="font-semibold text-yellow-600">-</div>
                            <div class="text-xs text-gray-500">Terlambat</div>
                        </div>
                        <div class="text-center">
                            <div id="quick-absent" class="font-semibold text-red-600">-</div>
                            <div class="text-xs text-gray-500">Tidak Hadir</div>
                        </div>
                    </div>
                @endif

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="px-6 py-2 flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="hidden md:ml-3 md:block text-sm font-medium text-gray-700">
                            {{ Str::limit(Auth::user()->name, 20) }}
                        </span>
                        <i class="hidden md:block ml-2 fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <!-- User Info -->
                            <div class="px-4 py-2 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->username }}</p>
                                @if(Auth::user()->employee)
                                    <p class="text-xs text-gray-500">{{ Auth::user()->employee->employee_id }}</p>
                                @endif
                            </div>

                            <!-- Menu Items -->
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                                Profil Saya
                            </a>

                            @if(Auth::user()->isEmployee() && Auth::user()->employee)
                                <a href="{{ route('reports.employee', Auth::user()->employee) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <i class="fas fa-chart-bar mr-2 text-gray-500"></i>
                                    Laporan Saya
                                </a>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live current time update
    function updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('id-ID', {
            weekday: 'short',
            day: 'numeric',
            month: 'short'
        });

        const currentTimeElement = document.getElementById('current-time');
        if (currentTimeElement) {
            currentTimeElement.textContent = `${dateString}, ${timeString}`;
        }
    }

    @if(Auth::user()->isAdmin())
    // Refresh quick stats for admin
    function refreshQuickStats() {
        fetch('{{ route("dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('quick-present').textContent = data.today_present || '0';
                document.getElementById('quick-late').textContent = data.today_late || '0';
                document.getElementById('quick-absent').textContent = data.today_absent || '0';

                // Stop spinning
                const refreshIcon = document.getElementById('refresh-icon');
                if (refreshIcon) {
                    refreshIcon.classList.remove('fa-spin');
                }
            })
            .catch(error => {
                console.error('Error refreshing stats:', error);
                const refreshIcon = document.getElementById('refresh-icon');
                if (refreshIcon) {
                    refreshIcon.classList.remove('fa-spin');
                }
            });
    }
    @endif

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);

        @if(Auth::user()->isAdmin())
            refreshQuickStats();
            // Auto-refresh stats every 2 minutes
            setInterval(refreshQuickStats, 120000);
        @endif
    });
</script>
