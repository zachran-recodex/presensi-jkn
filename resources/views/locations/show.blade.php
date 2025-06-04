<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                    Detail Lokasi - {{ $location->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap dan karyawan di lokasi ini</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('locations.edit', $location) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Lokasi
                </a>
                <a href="{{ route('locations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Location Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <!-- Location Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-{{ $location->is_active ? 'red' : 'gray' }}-100 text-{{ $location->is_active ? 'red' : 'gray' }}-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-2xl"></i>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $location->name }}</h1>

                            <!-- Status Badge -->
                            @if($location->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Informasi Alamat</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-start">
                                        <i class="fas fa-map-marked-alt w-4 text-gray-400 mr-2 mt-0.5"></i>
                                        <div>
                                            <span class="text-gray-600">Alamat:</span>
                                            <p class="font-medium text-gray-900 mt-1">
                                                {{ $location->address ?: 'Tidak ada alamat yang tercatat' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-globe w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Timezone:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $location->timezone }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Koordinat GPS</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i class="fas fa-crosshairs w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Latitude:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $location->latitude }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-crosshairs w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Longitude:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $location->longitude }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-circle-notch w-4 text-gray-400 mr-2"></i>
                                        <span class="text-gray-600">Radius:</span>
                                        <span class="ml-2 font-medium text-gray-900">{{ $location->radius }} meter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex-shrink-0 space-y-2">
                        <button onclick="viewOnMap({{ $location->latitude }}, {{ $location->longitude }})"
                                class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-map mr-2"></i>
                            Lihat di Maps
                        </button>

                        <button onclick="toggleStatus({{ $location->id }}, {{ $location->is_active ? 'false' : 'true' }})"
                                class="block w-full text-center px-4 py-2 bg-{{ $location->is_active ? 'yellow' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $location->is_active ? 'yellow' : 'green' }}-700 transition-colors">
                            <i class="fas fa-{{ $location->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>

                        @if($location->employees->count() == 0)
                        <button onclick="confirmDelete({{ $location->id }}, '{{ $location->name }}')"
                                class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Lokasi
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Location Statistics -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Statistik Lokasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $location->employees->count() }}</div>
                            <div class="text-sm text-blue-800">Total Karyawan</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $location->employees->where('status', 'active')->count() }}</div>
                            <div class="text-sm text-green-800">Karyawan Aktif</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $location->employees->where('status', 'inactive')->count() }}</div>
                            <div class="text-sm text-yellow-800">Tidak Aktif</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $location->employees->filter(function($emp) { return $emp->user->hasFaceEnrolled(); })->count() }}
                            </div>
                            <div class="text-sm text-purple-800">Face Enrolled</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distance Calculator -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200"
             x-data="distanceCalculator({{ $location->latitude }}, {{ $location->longitude }}, {{ $location->radius }})">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calculator mr-2 text-purple-600"></i>
                    Kalkulator Jarak
                </h3>
                <p class="text-sm text-gray-600 mt-1">Test jarak dari koordinat tertentu ke lokasi ini</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Input Coordinates -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Test Latitude</label>
                                <input type="number"
                                       x-model="testCoords.lat"
                                       step="any"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="-6.200000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Test Longitude</label>
                                <input type="number"
                                       x-model="testCoords.lng"
                                       step="any"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="106.816666">
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button @click="calculateDistance()"
                                    class="flex-1 bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-calculator mr-2"></i>
                                Hitung Jarak
                            </button>
                            <button @click="getCurrentLocation()"
                                    :disabled="gettingLocation"
                                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-blue-400">
                                <i class="fas fa-location-arrow mr-2"></i>
                                <span x-text="gettingLocation ? 'Detecting...' : 'Lokasi Saya'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="space-y-4">
                        <div x-show="distance !== null" class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Hasil Perhitungan</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jarak:</span>
                                    <span class="font-medium" x-text="distance + ' meter'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Radius Lokasi:</span>
                                    <span class="font-medium">{{ $location->radius }} meter</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium"
                                          :class="isWithinRadius ? 'text-green-600' : 'text-red-600'"
                                          x-text="isWithinRadius ? 'Dalam Radius ✓' : 'Di Luar Radius ✗'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Informasi</h4>
                            <div class="text-sm text-blue-800 space-y-1">
                                <p>• Koordinat lokasi: {{ $location->latitude }}, {{ $location->longitude }}</p>
                                <p>• Radius validasi: {{ $location->radius }} meter</p>
                                <p>• Presensi hanya valid dalam radius yang ditentukan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees at This Location -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-users mr-2 text-green-600"></i>
                        Karyawan di Lokasi Ini
                    </h3>
                    <span class="text-sm text-gray-600">{{ $location->employees->count() }} orang</span>
                </div>
            </div>

            @if($location->employees->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($location->employees as $employee)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-100 text-{{ $employee->status === 'active' ? 'blue' : 'gray' }}-600 p-3 rounded-full">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">{{ $employee->user->name }}</h4>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>
                                                <i class="fas fa-id-badge mr-1"></i>
                                                {{ $employee->employee_id }}
                                            </span>
                                    <span>
                                                <i class="fas fa-briefcase mr-1"></i>
                                                {{ $employee->position }}
                                            </span>
                                    <span>
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $employee->department ?: 'N/A' }}
                                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Status Badges -->
                            @if($employee->status === 'active')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            {{ ucfirst($employee->status) }}
                                        </span>
                            @endif

                            @if($employee->user->hasFaceEnrolled())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                            <i class="fas fa-face-smile mr-1"></i>
                                            Face OK
                                        </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-face-frown mr-1"></i>
                                            No Face
                                        </span>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('employees.show', $employee) }}"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-eye mr-2"></i>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center">
                <div class="bg-gray-100 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Karyawan</h3>
                <p class="text-gray-600 mb-6">Belum ada karyawan yang ditempatkan di lokasi ini.</p>
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Karyawan
                </a>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // View location on maps
        function viewOnMap(latitude, longitude) {
            const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
            window.open(mapsUrl, '_blank');
        }

        // Toggle location status
        async function toggleStatus(locationId, isActive) {
            const action = isActive === 'true' ? 'mengaktifkan' : 'menonaktifkan';

            if (!confirm(`Apakah Anda yakin ingin ${action} lokasi ini?`)) {
                return;
            }

            showLoading();

            try {
                const response = await fetch(`/locations/${locationId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah status: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                hideLoading();
            }
        }

        // Confirm delete location
        function confirmDelete(locationId, locationName) {
            if (confirm(`Apakah Anda yakin ingin menghapus lokasi "${locationName}"? Tindakan ini tidak dapat dibatalkan.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/locations/${locationId}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Distance calculator component
        function distanceCalculator(locationLat, locationLng, locationRadius) {
            return {
                testCoords: {
                    lat: '',
                    lng: ''
                },
                distance: null,
                gettingLocation: false,

                get isWithinRadius() {
                    return this.distance !== null && this.distance <= locationRadius;
                },

                calculateDistance() {
                    if (!this.testCoords.lat || !this.testCoords.lng) {
                        alert('Masukkan koordinat test terlebih dahulu');
                        return;
                    }

                    const R = 6371e3; // Earth's radius in meters
                    const φ1 = locationLat * Math.PI/180;
                    const φ2 = this.testCoords.lat * Math.PI/180;
                    const Δφ = (this.testCoords.lat - locationLat) * Math.PI/180;
                    const Δλ = (this.testCoords.lng - locationLng) * Math.PI/180;

                    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                        Math.cos(φ1) * Math.cos(φ2) *
                        Math.sin(Δλ/2) * Math.sin(Δλ/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                    this.distance = Math.round(R * c);
                },

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

                        this.testCoords.lat = position.coords.latitude.toFixed(6);
                        this.testCoords.lng = position.coords.longitude.toFixed(6);

                        // Auto calculate distance
                        this.calculateDistance();

                    } catch (error) {
                        console.error('Location error:', error);
                        alert('Gagal mendapatkan lokasi: ' + error.message);
                    } finally {
                        this.gettingLocation = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
