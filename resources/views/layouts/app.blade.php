<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Additional Page Styles -->
        @stack('styles')
    </head>
    <body class="h-full bg-gray-50 font-sans antialiased">
        <div class="min-h-full">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Main Content -->
            <main class="flex-1">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 5000)"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                                <span>{{ session('success') }}</span>
                                <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 5000)"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                                <span>{{ session('error') }}</span>
                                <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 5000)"
                         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg relative">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                                <span>{{ session('warning') }}</span>
                                <button @click="show = false" class="ml-auto text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Loading Overlay -->
        <div x-data="{ loading: false }"
             x-show="loading"
             x-transition.opacity
             x-on:start-loading.window="loading = true"
             x-on:stop-loading.window="loading = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                <div class="flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-900 font-medium">Memproses...</span>
                </div>
            </div>
        </div>

        <!-- Additional Page Scripts -->
        @stack('scripts')

        <!-- Global JavaScript -->
        <script>
            // Global helper functions
            window.showLoading = function() {
                window.dispatchEvent(new CustomEvent('start-loading'));
            };

            window.hideLoading = function() {
                window.dispatchEvent(new CustomEvent('stop-loading'));
            };

            // CSRF token for AJAX requests
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };

            // Setup CSRF for all AJAX requests
            const token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
            }
        </script>
    </body>
</html>
