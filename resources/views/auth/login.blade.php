<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Login') }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Company Logo & Name -->
        <div class="mb-8 text-center">
            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-building text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">PT. Jaka Kuasa Nusantara</h1>
            <p class="text-gray-600">Sistem Presensi Karyawan</p>
        </div>

        <!-- Login Form -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg border border-gray-200">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 text-center">Masuk ke Akun Anda</h2>
                <p class="text-gray-600 text-center text-sm mt-2">Silakan masukkan kredensial Anda</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block font-medium text-sm text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>
                        Email
                    </label>
                    <input id="email"
                           class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('email') border-red-500 @enderror"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="nama@email.com">
                    @error('email')
                    <p class="mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block font-medium text-sm text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input id="password"
                               class="block mt-1 w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('password') border-red-500 @enderror"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan password">
                        <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePassword()">
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me"
                               type="checkbox"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:text-blue-800 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                           href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col space-y-3">
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} PT. Jaka Kuasa Nusantara. All rights reserved.</p>
            </div>
        </div>

        <!-- System Info -->
        <div class="mt-6 text-center text-xs text-gray-500">
            <p>Sistem Presensi v1.0 | Powered by Laravel</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
