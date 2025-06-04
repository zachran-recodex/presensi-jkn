<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                    Manajemen Lokasi Kantor
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola lokasi kantor untuk validasi presensi GPS</p>
            </div>
            <div>
                <a href="{{ route('locations.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Lokasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Lokasi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $locations->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Aktif</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $locations->where('is_active', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 text-red-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $locations->where('is_active', false)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Daftar Lokasi Kantor
                </h3>
            </div>

            @if($locations->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($locations as $location)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <!-- Location Info -->
                                <div class="flex items-center space-x-4">
                                    <div class="bg-{{ $location->is_active ? 'red' : 'gray' }}-100 text-{{ $location->is_active ? 'red' : 'gray' }}-600 p-3 rounded-full">
                                        <i class="fas fa-map-marker-alt text-xl"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $location->name }}
                                            </h4>

                                            <!-- Status Badge -->
                                            @if($location->is_active)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <i class="fas fa-map-marked-alt mr-1"></i>
                                                    Alamat
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $location->address ?: 'Tidak ada alamat' }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <i class="fas fa-crosshairs mr-1"></i>
                                                    Koordinat
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $location->latitude }}, {{ $location->longitude }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <i class="fas fa-circle-notch mr-1"></i>
                                                    Radius
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $location->radius }} meter
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Employee Count -->
                                        <div class="mt-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $location->employees_count }} karyawan
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0" x-data="{ showDropdown: false }">
                                    <div class="relative">
                                        <button @click="showDropdown = !showDropdown"
                                                class="inline-flex items-center px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>

                                        <div x-show="showDropdown"
                                             x-transition
                                             @click.away="showDropdown = false"
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                            <div class="py-1">
                                                <a href="{{ route('locations.show', $location) }}"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-eye mr-2 text-blue-500"></i>
                                                    Lihat Detail
                                                </a>

                                                <a href="{{ route('locations.edit', $location) }}"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-edit mr-2 text-green-500"></i>
                                                    Edit
                                                </a>

                                                <button onclick="viewOnMap({{ $location->latitude }}, {{ $location->longitude }})"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-map mr-2 text-purple-500"></i>
                                                    Lihat di Maps
                                                </button>

                                                <div class="border-t border-gray-100"></div>

                                                <button onclick="toggleLocationStatus({{ $location->id }}, {{ $location->is_active ? 'false' : 'true' }})"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-{{ $location->is_active ? 'pause' : 'play' }} mr-2 text-yellow-500"></i>
                                                    {{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>

                                                @if($location->employees_count == 0)
                                                    <button onclick="confirmDelete({{ $location->id }}, '{{ $location->name }}')"
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <i class="fas fa-trash mr-2"></i>
                                                        Hapus
                                                    </button>
                                                @else
                                                    <div class="px-4 py-2 text-xs text-gray-500">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Tidak dapat dihapus (ada karyawan)
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $locations->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Lokasi</h3>
                    <p class="text-gray-600 mb-6">Tambahkan lokasi kantor untuk validasi presensi GPS karyawan.</p>
                    <a href="{{ route('locations.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Lokasi Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">
                <i class="fas fa-lightbulb mr-2"></i>
                Tips Pengaturan Lokasi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                <div>
                    <h4 class="font-medium mb-1">Radius yang Disarankan</h4>
                    <p>
                        Gunakan radius 50-100 meter untuk gedung kantor kecil,
                        100-200 meter untuk komplek kantor besar.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Akurasi GPS</h4>
                    <p>
                        GPS smartphone umumnya memiliki akurasi 3-5 meter di area terbuka,
                        10-15 meter di dalam gedung.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Testing Lokasi</h4>
                    <p>
                        Setelah menambah lokasi, test dengan melakukan presensi
                        dari berbagai titik untuk memastikan akurasi.
                    </p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Multiple Lokasi</h4>
                    <p>
                        Jika kantor memiliki multiple gedung, buat lokasi terpisah
                        untuk setiap gedung dengan radius yang sesuai.
                    </p>
                </div>
            </div>
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
            async function toggleLocationStatus(locationId, isActive) {
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

            // Calculate distance between two points
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3; // Earth's radius in meters
                const φ1 = lat1 * Math.PI/180;
                const φ2 = lat2 * Math.PI/180;
                const Δφ = (lat2-lat1) * Math.PI/180;
                const Δλ = (lon2-lon1) * Math.PI/180;

                const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                return R * c;
            }
        </script>
    @endpush
</x-app-layout>
