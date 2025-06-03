<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <div class="min-h-full">
            <!-- Navigation -->
            <nav class="bg-white/90 backdrop-blur-sm shadow-lg sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-600 text-white p-2 rounded-lg">
                                <i class="fas fa-user-clock text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">Sistem Presensi</h1>
                                <p class="text-xs text-gray-600 hidden sm:block">PT. Jaka Kuasa Nusantara</p>
                            </div>
                        </div>

                        <!-- Navigation Links -->
                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-chart-line mr-2"></i>
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Login
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                    <div class="text-center">
                        <!-- Main Hero Content -->
                        <div class="mb-16">
                            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                                Sistem Presensi
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                    Modern
                                </span>
                            </h1>
                            <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-4xl mx-auto">
                                Solusi presensi karyawan dengan teknologi
                                <strong>Face Recognition</strong> dan <strong>GPS Tracking</strong>
                                untuk PT. Jaka Kuasa Nusantara
                            </p>

                            @guest
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-xl hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                                        <i class="fas fa-sign-in-alt mr-3"></i>
                                        Masuk ke Sistem
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-xl hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                                        <i class="fas fa-chart-line mr-3"></i>
                                        Buka Dashboard
                                    </a>
                                </div>
                            @endguest
                        </div>

                        <!-- Features Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                            <!-- Face Recognition -->
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                                <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-face-smile text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Face Recognition</h3>
                                <p class="text-gray-600">
                                    Teknologi pengenalan wajah canggih dari Biznet untuk memastikan
                                    hanya karyawan yang sah yang dapat melakukan presensi.
                                </p>
                            </div>

                            <!-- GPS Tracking -->
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                                <div class="bg-green-100 text-green-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-map-marker-alt text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">GPS Tracking</h3>
                                <p class="text-gray-600">
                                    Validasi lokasi real-time memastikan karyawan berada di area
                                    kantor yang telah ditentukan saat melakukan presensi.
                                </p>
                            </div>

                            <!-- Real-time Monitoring -->
                            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                                <div class="bg-purple-100 text-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-chart-line text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Real-time Monitoring</h3>
                                <p class="text-gray-600">
                                    Dashboard admin dengan analisis kehadiran real-time, laporan lengkap,
                                    dan notifikasi untuk monitoring yang efektif.
                                </p>
                            </div>
                        </div>

                        <!-- Tech Stack -->
                        <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-white/20">
                            <h3 class="text-2xl font-bold text-gray-900 mb-8">Teknologi Yang Digunakan</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div class="text-center">
                                    <div class="bg-red-100 text-red-600 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fab fa-laravel text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Laravel 10</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fab fa-js-square text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Alpine.js</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-cyan-100 text-cyan-600 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fab fa-css3-alt text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Tailwind CSS</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-orange-100 text-orange-600 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-database text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">MySQL</p>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Info -->
                        @guest
                            <div class="mt-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                                <h3 class="text-2xl font-bold mb-4">
                                    <i class="fas fa-rocket mr-2"></i>
                                    Coba Demo Sistem
                                </h3>
                                <p class="text-blue-100 mb-6">
                                    Sistem ini dilengkapi dengan data demo untuk testing.
                                    Gunakan akun demo untuk menjelajahi fitur-fitur yang tersedia.
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <h4 class="font-semibold mb-2">
                                            <i class="fas fa-user-shield mr-2"></i>
                                            Admin Demo
                                        </h4>
                                        <p class="text-sm text-blue-100 mb-2">admin@jakakuasanusantara.web.id</p>
                                        <p class="text-xs text-blue-200">Akses penuh ke semua fitur sistem</p>
                                    </div>
                                    <div class="bg-white/10 rounded-lg p-4">
                                        <h4 class="font-semibold mb-2">
                                            <i class="fas fa-user mr-2"></i>
                                            Karyawan Demo
                                        </h4>
                                        <p class="text-sm text-blue-100 mb-2">budi.santoso@jakakuasanusantara.web.id</p>
                                        <p class="text-xs text-blue-200">Akses fitur presensi dan history</p>
                                    </div>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>

                <!-- Background Pattern -->
                <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
                    <div class="absolute -top-40 -right-32 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
                    <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
                    <div class="absolute top-40 left-40 w-80 h-80 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white/90 backdrop-blur-sm border-t border-gray-200 mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-3 mb-4">
                            <div class="bg-blue-600 text-white p-2 rounded-lg">
                                <i class="fas fa-user-clock text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">PT. Jaka Kuasa Nusantara</h3>
                                <p class="text-sm text-gray-600">Sistem Presensi Karyawan</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center space-x-8 text-sm text-gray-600 mb-4">
                            <span class="flex items-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Secure & Reliable
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-mobile-alt mr-1"></i>
                                Mobile Friendly
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-chart-line mr-1"></i>
                                Real-time Analytics
                            </span>
                        </div>

                        <p class="text-gray-500 text-sm">
                            &copy; {{ date('Y') }} PT. Jaka Kuasa Nusantara. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
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
    </body>
</html>
