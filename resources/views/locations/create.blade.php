<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Lokasi Baru') }}
        </h2>
    </x-slot>

    @section('header-actions')
        <a href="{{ route('locations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('locations.store') }}" id="locationForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lokasi -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Lokasi')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Timezone -->
                            <div>
                                <x-input-label for="timezone" :value="__('Timezone')" />
                                <select id="timezone" name="timezone" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="Asia/Jakarta" {{ old('timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ old('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ old('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                                <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Alamat')" />
                                <textarea id="address" name="address" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <!-- Koordinat -->
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')" />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude')" required placeholder="-6.2088" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')" />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude')" required placeholder="106.8456" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>

                            <!-- Radius -->
                            <div>
                                <x-input-label for="radius" :value="__('Radius (meter)')" />
                                <x-text-input id="radius" class="block mt-1 w-full" type="number" name="radius" :value="old('radius', 100)" required min="10" max="1000" />
                                <p class="text-sm text-gray-500 mt-1">Radius area untuk validasi presensi (10-1000 meter)</p>
                                <x-input-error :messages="$errors->get('radius')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <div class="flex items-center mt-6">
                                    <input id="is_active" name="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Lokasi Aktif</label>
                                </div>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Map Preview -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Peta Lokasi</h3>
                            <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
                            <p class="text-sm text-gray-500 mt-2">Klik pada peta untuk mengatur lokasi atau masukkan koordinat secara manual</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="button" id="validateCoordinates" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                <i class="fas fa-map-marker-alt mr-2"></i> Validasi Koordinat
                            </button>
                            <x-primary-button>
                                {{ __('Simpan Lokasi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-control-attribution {
            font-size: 10px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Default to Jakarta coordinates if no values
            let lat = document.getElementById('latitude').value || -6.2088;
            let lng = document.getElementById('longitude').value || 106.8456;
            let radius = document.getElementById('radius').value || 100;

            // Initialize map
            const map = L.map('map').setView([lat, lng], 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker and circle
            let marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            let circle = L.circle([lat, lng], {
                radius: radius,
                color: '#3b82f6',
                fillColor: '#93c5fd',
                fillOpacity: 0.3
            }).addTo(map);

            // Update marker and circle when dragging marker
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat.toFixed(8);
                document.getElementById('longitude').value = position.lng.toFixed(8);
                circle.setLatLng(position);
            });

            // Update marker and circle when clicking on map
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                circle.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
            });

            // Update circle radius when radius input changes
            document.getElementById('radius').addEventListener('input', function() {
                const newRadius = parseInt(this.value) || 100;
                circle.setRadius(newRadius);
            });

            // Update map when coordinates are changed manually
            document.getElementById('latitude').addEventListener('change', updateMapFromInputs);
            document.getElementById('longitude').addEventListener('change', updateMapFromInputs);

            function updateMapFromInputs() {
                const newLat = parseFloat(document.getElementById('latitude').value) || lat;
                const newLng = parseFloat(document.getElementById('longitude').value) || lng;
                marker.setLatLng([newLat, newLng]);
                circle.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], 15);
            }

            // Validate coordinates button
            document.getElementById('validateCoordinates').addEventListener('click', function() {
                const latitude = document.getElementById('latitude').value;
                const longitude = document.getElementById('longitude').value;

                if (!latitude || !longitude) {
                    alert('Silakan masukkan koordinat latitude dan longitude terlebih dahulu.');
                    return;
                }

                // Send AJAX request to validate coordinates
                fetch('/locations/validate-coordinates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showNotification('Koordinat valid!', 'success');
                    } else {
                        window.showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showNotification('Terjadi kesalahan saat memvalidasi koordinat', 'error');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
