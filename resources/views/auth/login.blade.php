<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .animated-bg {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .slide-in {
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-focus-effect:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-hover-effect {
            transition: all 0.3s ease;
        }

        .btn-hover-effect:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .icon-bounce {
            animation: iconBounce 2s infinite;
        }

        @keyframes iconBounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
    </style>
</head>
<body class="font-sans antialiased animated-bg min-h-screen">
    <!-- Background Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white opacity-10 floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-white opacity-10 floating-animation" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/4 w-64 h-64 rounded-full bg-white opacity-5 floating-animation" style="animation-delay: -1.5s;"></div>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
        <!-- Logo Section -->
        <div class="slide-in mb-8">
            <a href="/" class="block text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-white rounded-full shadow-2xl flex items-center justify-center icon-bounce">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-12 h-12 object-contain" />
                </div>
                <h1 class="text-white text-2xl font-bold">{{ config('app.name', 'Laravel') }}</h1>
                <p class="text-white text-opacity-80 mt-2">Selamat datang kembali!</p>
            </a>
        </div>

        <!-- Login Card -->
        <div class="w-full sm:max-w-md slide-in" style="animation-delay: 0.2s;">
            <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-8 py-10">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Masuk ke Akun</h2>
                        <p class="text-white text-opacity-70">Silakan masukkan kredensial Anda</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Username -->
                        <div class="space-y-2">
                            <x-input-label for="username" :value="__('Username')" class="text-white font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <x-text-input id="username"
                                    class="block w-full pl-10 pr-4 py-3 input-focus-effect transition-all duration-300 bg-white bg-opacity-90 border-0 rounded-xl shadow-lg text-gray-800 placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-white focus:ring-opacity-50"
                                    type="text"
                                    name="username"
                                    :value="old('username')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Masukkan username Anda" />
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <x-input-label for="password" :value="__('Password')" class="text-white font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <x-text-input id="password"
                                    class="block w-full pl-10 pr-4 py-3 input-focus-effect transition-all duration-300 bg-white bg-opacity-90 border-0 rounded-xl shadow-lg text-gray-800 placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-white focus:ring-opacity-50"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password Anda" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0"
                                    name="remember">
                                <span class="ml-3 text-sm text-white font-medium">{{ __('Ingat saya') }}</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl btn-hover-effect shadow-xl">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                {{ __('Masuk') }}
                            </button>
                        </div>

                        <!-- Additional Links -->
                        <div class="text-center pt-4 space-y-2">
                            <a href="#" class="text-white text-opacity-80 hover:text-white text-sm transition-colors duration-300">
                                <i class="fas fa-key mr-1"></i>
                                Lupa password?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center slide-in" style="animation-delay: 0.4s;">
            <p class="text-white text-opacity-60 text-sm">
                © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Dibuat dengan <i class="fas fa-heart text-red-400"></i>
            </p>
        </div>
    </div>
</body>
</html>
