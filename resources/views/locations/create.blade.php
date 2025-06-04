<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-map-marker-plus mr-2 text-blue-600"></i>
                    Tambah Lokasi Kantor
                </h2>
                <p class="text-sm text-gray-600 mt-1">Daftarkan lokasi baru untuk validasi presensi GPS</p>
            </div>
            <div>
                <a href="{{ route('locations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST"
              action="{{ route('locations.store') }}"
              x-data="locationForm()"
              @submit="showLoading()">
            @csrf

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Informasi Lokasi
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Data dasar lokasi kantor</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Location Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lokasi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Kantor Pusat Jakarta, Cabang Bandung, dll">
                                @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap
                                </label>
                                <textarea id="address"
                                          name="address"
                                          rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                          placeholder="Masukkan alamat lengkap lokasi kantor...">{{ old('address') }}</textarea>
                                @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Timezone -->
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Timezone <span class="text-red-500">*</span>
                                </label>
                                <select id="timezone"
                                        name="timezone"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('timezone') border-red-500 @enderror">
                                    <option value="">Pilih Timezone</option>
                                    <option value="Asia/Jakarta" {{ old('timezone') === 'Asia/Jakarta' ? 'selected' : '' }}>
                                        WIB - Asia/Jakarta (UTC+7)
                                    </option>
                                    <option value="Asia/Makassar" {{ old('timezone') === 'Asia/Makassar' ? 'selected' : '' }}>
                                        WITA - Asia/Makassar (UTC+8)
                                    </option>
                                    <option value="Asia/Jayapura" {{ old('timezone') === 'Asia/Jayapura' ? 'selected' : '' }}>
                                        WIT - Asia/Jayapura (UTC+9)
                                    </option>
                                </select>
                                @error('timezone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Lokasi
                                </label>
                                <select id="is_active"
                                        name="is_active"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>
                                        Tidak Aktif
                                    </option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Hanya lokasi aktif yang dapat digunakan untuk presensi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GPS Coordinates -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-crosshairs mr-2 text-red-600"></i>
                            Koordinat GPS
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Tentukan titik koordinat dan radius validasi presensi</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Current Location Detection -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-blue-900">Deteksi Lokasi Otomatis</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Gunakan GPS perangkat untuk mendapatkan koordinat saat ini
                                    </p>
                                </div>
                                <button type="button"
                                        @click="getCurrentLocation()"
                                        :disabled="gettingLocation"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-blue-400">
                                    <i class="fas fa-location-arrow mr-2"></i>
                                    <span x-text="gettingLocation ? 'Detecting...' : 'Deteksi Lokasi'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="latitude"
                                       name="latitude"
                                       x-model="coordinates.latitude"
                                       step="any"
                                       min="-90"
                                       max="90"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('latitude') border-red-500 @enderror"
                                       placeholder="-6.200000">
                                @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="longitude"
                                       name="longitude"
                                       x-model="coordinates.longitude"
                                       step="any"
                                       min="-180"
                                       max="180"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('longitude') border-red-500 @enderror"
                                       placeholder="106.816666">
                                @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Radius -->
                            <div>
                                <label for="radius" class="block text-sm font-medium text-gray-700 mb-2">
                                    Radius (meter) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="radius"
                                       name="radius"
                                       value="{{ old('radius', 100) }}"
                                       min="10"
                                       max="1000"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('radius') border-red-500 @enderror"
                                       placeholder="100">
                                @error('radius')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    Jarak maksimal (10-1000 meter) dari titik untuk presensi valid
                                </p>
                            </div>
                        </div>

                        <!-- Coordinates Status -->
                        <div x-show="coordinates.latitude && coordinates.longitude" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-green-900">Koordinat Terdeteksi</h4>
                                    <p class="text-sm text-green-700 mt-1">
                                        Lat: <span x-text="coordinates.latitude"></span>,
                                        Lng: <span x-text="coordinates.longitude"></span>
                                    </p>
                                </div>
                                <button type="button"
                                        @click="viewOnMaps()"
                                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                    <i class="fas fa-map mr-2"></i>
                                    Lihat di Maps
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation & Testing -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-vial mr-2 text-purple-600"></i>
                            Validasi & Testing
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Test koordinat dan radius sebelum menyimpan</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Distance Calculator -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Kalkulator Jarak
                                </h4>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="number"
                                               x-model="testCoords.lat"
                                               step="any"
                                               placeholder="Test Latitude"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                        <input type="number"
                                               x-model="testCoords.lng"
                                               step="any"
                                               placeholder="Test Longitude"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                    </div>
                                    <button type="button"
                                            @click="calculateDistance()"
                                            class="w-full bg-purple-600 text-white py-2 px-3 rounded text-sm hover:bg-purple-700">
                                        Hitung Jarak
                                    </button>
                                    <div x-show="testDistance !== null" class="text-sm">
                                        <p class="text-gray-600">Jarak: <span x-text="testDistance"></span> meter</p>
                                        <p :class="testDistance <= parseInt(document.getElementById('radius').value) ? 'text-green-600' : 'text-red-600'">
                                            <span x-text="testDistance <= parseInt(document.getElementById('radius').value) ? 'Dalam radius ✓' : 'Di luar radius ✗'"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Accuracy Tips -->
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-yellow-900 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Tips Akurasi GPS
                                </h4>
                                <ul class="text-sm text-yellow-800 space-y-1">
                                    <li>• GPS indoor memiliki akurasi ±10-15 meter</li>
                                    <li>• GPS outdoor memiliki akurasi ±3-5 meter</li>
                                    <li>• Gunakan radius minimal 50 meter untuk gedung</li>
                                    <li>• Test dari berbagai titik sebelum go-live</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan koordinat dan radius sudah benar sebelum menyimpan lokasi.
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('locations.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Lokasi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function locationForm() {
                return {
                    coordinates: {
                        latitude: '{{ old("latitude") }}',
                        longitude: '{{ old("longitude") }}'
                    },
                    gettingLocation: false,
                    testCoords: {
                        lat: '',
                        lng: ''
                    },
                    testDistance: null,

                    async getCurrentLocation() {
                        if (!navigator.geolocation) {
                            alert('Browser tidak mendukung geolocation');
                            return;
                        }

                        this.gettingLocation = true;

                        try {
                            const position = await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition(resolve, reject, {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 300000
                                });
                            });

                            this.coordinates.latitude = position.coords.latitude.toFixed(6);
                            this.coordinates.longitude = position.coords.longitude.toFixed(6);

                            // Update form inputs
                            document.getElementById('latitude').value = this.coordinates.latitude;
                            document.getElementById('longitude').value = this.coordinates.longitude;

                        } catch (error) {
                            console.error('Location error:', error);
                            alert('Gagal mendapatkan lokasi: ' + error.message);
                        } finally {
                            this.gettingLocation = false;
                        }
                    },

                    viewOnMaps() {
                        if (!this.coordinates.latitude || !this.coordinates.longitude) {
                            alert('Koordinat belum tersedia');
                            return;
                        }

                        const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${this.coordinates.latitude},${this.coordinates.longitude}`;
                        window.open(mapsUrl, '_blank');
                    },

                    calculateDistance() {
                        if (!this.coordinates.latitude || !this.coordinates.longitude || !this.testCoords.lat || !this.testCoords.lng) {
                            alert('Masukkan semua koordinat untuk menghitung jarak');
                            return;
                        }

                        const R = 6371e3; // Earth's radius in meters
                        const φ1 = this.coordinates.latitude * Math.PI/180;
                        const φ2 = this.testCoords.lat * Math.PI/180;
                        const Δφ = (this.testCoords.lat - this.coordinates.latitude) * Math.PI/180;
                        const Δλ = (this.testCoords.lng - this.coordinates.longitude) * Math.PI/180;

                        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                            Math.cos(φ1) * Math.cos(φ2) *
                            Math.sin(Δλ/2) * Math.sin(Δλ/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                        this.testDistance = Math.round(R * c);
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
