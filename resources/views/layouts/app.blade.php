<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Presensi') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
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

        <!-- Sidebar for mobile and desktop -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            @include('components.sidebar')
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-white lg:hidden">
            @include('components.sidebar')
        </div>

        <!-- Main content area -->
        <div class="lg:pl-64 flex flex-col min-h-screen">
            <!-- Top navigation -->
            @include('components.navigation')

            <!-- Page Content -->
            <main class="flex-1">
                <!-- Page header -->
                @hasSection('header')
                    <div class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between">
                                <h1 class="text-3xl font-bold text-gray-900">
                                    @yield('header')
                                </h1>
                                @hasSection('header-actions')
                                    <div class="flex space-x-3">
                                        @yield('header-actions')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alert Messages -->
                @include('components.alert')

                <!-- Main content -->
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
                            © {{ date('Y') }} PT. Jaka Kuasa Nusantara. All rights reserved.
                        </p>
                        <p class="text-sm text-gray-500">
                            Version 1.0.0
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Component -->
    @include('components.modal')

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Global JavaScript -->
    <script>
        // CSRF Token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Set CSRF token for all AJAX requests
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;

        // Global notification function
        window.showNotification = function(message, type = 'success') {
            const event = new CustomEvent('show-notification', {
                detail: { message, type }
            });
            window.dispatchEvent(event);
        };

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('[x-data]');
            if (sidebar && !sidebar.contains(event.target)) {
                Alpine.store('sidebar', { open: false });
            }
        });
    </script>
</body>
</html>
