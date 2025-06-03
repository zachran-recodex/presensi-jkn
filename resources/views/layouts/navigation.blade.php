<nav x-data="{ open: false, profileOpen: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="bg-blue-600 text-white p-2 rounded-lg">
                            <i class="fas fa-user-clock text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold text-gray-900">Presensi</h1>
                            <p class="text-xs text-gray-600">PT. Jaka Kuasa Nusantara</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <!-- Dashboard -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-chart-line mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                        <!-- Admin Menu -->
                        <div class="relative" x-data="{ employeeOpen: false }">
                            <button @click="employeeOpen = !employeeOpen"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                           {{ request()->routeIs('employees.*') || request()->routeIs('locations.*') || request()->routeIs('face-enrollment.*')
                                              ? 'border-blue-400 text-gray-900 focus:border-blue-700'
                                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                <i class="fas fa-users mr-2"></i>
                                {{ __('Kelola Karyawan') }}
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>

                            <div x-show="employeeOpen"
                                 x-transition
                                 @click.away="employeeOpen = false"
                                 class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('employees.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user-friends mr-2"></i>
                                        Data Karyawan
                                    </a>
                                    <a href="{{ route('locations.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Lokasi Kantor
                                    </a>
                                    <a href="{{ route('face-enrollment.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-face-smile mr-2"></i>
                                        Face Enrollment
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reports -->
                        <div class="relative" x-data="{ reportOpen: false }">
                            <button @click="reportOpen = !reportOpen"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                           {{ request()->routeIs('reports.*')
                                              ? 'border-blue-400 text-gray-900 focus:border-blue-700'
                                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                <i class="fas fa-chart-bar mr-2"></i>
                                {{ __('Laporan') }}
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>

                            <div x-show="reportOpen"
                                 x-transition
                                 @click.away="reportOpen = false"
                                 class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('reports.daily') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-calendar-day mr-2"></i>
                                        Laporan Harian
                                    </a>
                                    <a href="{{ route('reports.monthly') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Laporan Bulanan
                                    </a>
                                    <a href="{{ route('admin.attendance.history') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-history mr-2"></i>
                                        History Presensi
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Employee Menu -->
                        <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
                            <i class="fas fa-clock mr-2"></i>
                            {{ __('Presensi') }}
                        </x-nav-link>

                        <x-nav-link :href="route('attendance.history')" :active="request()->routeIs('attendance.history')">
                            <i class="fas fa-history mr-2"></i>
                            {{ __('History') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Quick Stats (Admin Only) -->
                @if(auth()->user()->isAdmin())
                    <div class="mr-4 text-sm text-gray-600">
                        <span id="live-time" class="font-medium"></span>
                    </div>
                @endif

                <!-- Profile Dropdown -->
                <div class="ml-3 relative" x-data="{ profileOpen: false }">
                    <div>
                        <button @click="profileOpen = !profileOpen"
                                class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <div class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50">
                                <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ Auth::user()->isAdmin() ? 'Administrator' : 'Karyawan' }}
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </button>
                    </div>

                    <div x-show="profileOpen"
                         x-transition
                         @click.away="profileOpen = false"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">

                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-edit mr-3 text-gray-400"></i>
                            {{ __('Profile') }}
                        </a>

                        @if(auth()->user()->employee)
                            <a href="{{ route('reports.employee', auth()->user()->employee) }}"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chart-line mr-3 text-gray-400"></i>
                                {{ __('Laporan Saya') }}
                            </a>
                        @endif

                        <div class="border-t border-gray-100"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 border-t border-gray-200">
            <!-- Dashboard -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-chart-line mr-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->isAdmin())
                <!-- Admin Menu -->
                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    <i class="fas fa-users mr-2"></i>
                    {{ __('Data Karyawan') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ __('Lokasi Kantor') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('face-enrollment.index')" :active="request()->routeIs('face-enrollment.*')">
                    <i class="fas fa-face-smile mr-2"></i>
                    {{ __('Face Enrollment') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('reports.daily')" :active="request()->routeIs('reports.*')">
                    <i class="fas fa-chart-bar mr-2"></i>
                    {{ __('Laporan') }}
                </x-responsive-nav-link>
            @else
                <!-- Employee Menu -->
                <x-responsive-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
                    <i class="fas fa-clock mr-2"></i>
                    {{ __('Presensi') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('attendance.history')" :active="request()->routeIs('attendance.history')">
                    <i class="fas fa-history mr-2"></i>
                    {{ __('History') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="text-xs text-gray-400">
                    {{ Auth::user()->isAdmin() ? 'Administrator' : 'Karyawan' }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user-edit mr-2"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if(auth()->user()->employee)
                    <x-responsive-nav-link :href="route('reports.employee', auth()->user()->employee)">
                        <i class="fas fa-chart-line mr-2"></i>
                        {{ __('Laporan Saya') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Live Time Script -->
<script>
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('live-time');
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }

    // Update time every second
    if (document.getElementById('live-time')) {
        updateTime();
        setInterval(updateTime, 1000);
    }
</script>