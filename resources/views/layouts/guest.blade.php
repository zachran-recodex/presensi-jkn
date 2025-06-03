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
    </head>
    <body class="h-full bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 font-sans antialiased">
        <div class="min-h-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Header/Logo Section -->
            <div class="mb-8 text-center">
                <div class="mx-auto bg-white rounded-full p-4 shadow-lg mb-4 w-20 h-20 flex items-center justify-center">
                    <i class="fas fa-user-clock text-3xl text-blue-600"></i>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-2">Sistem Presensi</h1>
                <p class="text-lg text-gray-600 mb-1">PT. Jaka Kuasa Nusantara</p>
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>Secure & Reliable</span>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
                <!-- Status Messages -->
                @if (session('status'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2 text-green-600"></i>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
                            {{ session('warning') }}
                        </div>
                    </div>
                @endif

                <!-- Form Content -->
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} PT. Jaka Kuasa Nusantara. All rights reserved.</p>
                <div class="mt-2 flex items-center justify-center space-x-4">
                    <span class="flex items-center">
                        <i class="fas fa-fingerprint mr-1"></i>
                        Face Recognition
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-mobile-alt mr-1"></i>
                        Mobile Ready
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        GPS Tracking
                    </span>
                </div>
            </div>

            <!-- Background Pattern -->
            <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-32 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
                <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
                <div class="absolute top-40 left-40 w-80 h-80 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div x-data="{ loading: false }"
             x-show="loading"
             x-transition.opacity
             x-on:start-loading.window="loading = true"
             x-on:stop-loading.window="loading = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
                <div class="flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-900 font-medium">Memproses...</span>
                </div>
            </div>
        </div>

        <style>
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>

        <script>
            // Global helper functions
            window.showLoading = function() {
                window.dispatchEvent(new CustomEvent('start-loading'));
            };

            window.hideLoading = function() {
                window.dispatchEvent(new CustomEvent('stop-loading'));
            };
        </script>
    </body>
</html>
