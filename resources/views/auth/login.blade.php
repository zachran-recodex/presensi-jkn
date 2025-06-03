<x-guest-layout>
    <!-- Login Form -->
    <div x-data="loginForm()">
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke Sistem</h2>
            <p class="text-gray-600">Masukkan email dan password untuk melanjutkan</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" @submit="showLoading()">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i>
                    Email
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="username"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('email') border-red-500 @enderror"
                       placeholder="email@jakakuasanusantara.web.id">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-gray-500"></i>
                    Password
                </label>
                <div class="relative">
                    <input id="password"
                           :type="showPassword ? 'text' : 'password'"
                           name="password"
                           required
                           autocomplete="current-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password">
                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 transition-colors">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me"
                           type="checkbox"
                           name="remember"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Masuk
            </button>
        </form>

        <!-- Demo Accounts Info -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="text-sm font-medium text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Akun Demo
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center p-2 bg-white rounded border">
                    <span class="text-gray-700">
                        <strong>Admin:</strong> admin@jakakuasanusantara.web.id
                    </span>
                    <button type="button"
                            @click="fillDemo('admin@jakakuasanusantara.web.id', 'admin123456')"
                            class="text-blue-600 hover:text-blue-800 text-xs">
                        Gunakan
                    </button>
                </div>
                <div class="flex justify-between items-center p-2 bg-white rounded border">
                    <span class="text-gray-700">
                        <strong>Karyawan:</strong> budi.santoso@jakakuasanusantara.web.id
                    </span>
                    <button type="button"
                            @click="fillDemo('budi.santoso@jakakuasanusantara.web.id', 'employee123')"
                            class="text-blue-600 hover:text-blue-800 text-xs">
                        Gunakan
                    </button>
                </div>
            </div>
            <p class="text-xs text-blue-700 mt-3">
                <i class="fas fa-shield-alt mr-1"></i>
                Akun demo untuk testing sistem. Gunakan akun Anda yang sebenarnya untuk produksi.
            </p>
        </div>

        <!-- System Info -->
        <div class="mt-6 text-center">
            <div class="flex items-center justify-center space-x-6 text-xs text-gray-500">
                <span class="flex items-center">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure Login
                </span>
                <span class="flex items-center">
                    <i class="fas fa-fingerprint mr-1"></i>
                    Face Recognition
                </span>
                <span class="flex items-center">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    GPS Tracking
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function loginForm() {
            return {
                showPassword: false,

                fillDemo(email, password) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                },

                init() {
                    // Auto-focus on email if empty
                    const emailInput = document.getElementById('email');
                    if (!emailInput.value) {
                        emailInput.focus();
                    }
                }
            }
        }

        // Handle enter key on demo buttons
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'submit') {
                e.preventDefault();
                document.querySelector('button[type="submit"]').click();
            }
        });
    </script>
    @endpush
</x-guest-layout>
