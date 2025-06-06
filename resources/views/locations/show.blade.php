<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Lokasi') }}: {{ $location->name }}
        </h2>
    </x-slot>

    @section('header-actions')
        <div class="flex space-x-2">
            <a href="{{ route('locations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('locations.edit', $location) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informasi Lokasi -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Lokasi</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nama Lokasi</p>
                                    <p class="text-base font-medium">{{ $location->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p>
                                        @if($location->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-green-400 mr-1.5" style="font-size: 0.5rem;"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-circle text-red-400 mr-1.5" style="font-size: 0.5rem;"></i>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Alamat</p>
                                    <p class="text-base">{{ $location->address }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Koordinat</p>
                                    <p class="text-base">{{ $location->latitude }}, {{ $location->longitude }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Radius</p>
                                    <p class="text-base">{{ $location->radius }} meter</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Timezone</p>
                                    <p class="text-base">{{ $location->timezone }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500">Jumlah Karyawan</p>
                                    <p class="text-base">{{ $location->employees_count }} karyawan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Peta Lokasi -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Peta Lokasi</h3>
                            <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Karyawan -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Karyawan di Lokasi Ini</h3>
                                <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $location->employees_count }}</span>
                            </div>

                            @if($location->employees_count > 0)
                                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                                    @foreach($location->employees as $employee)
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                @if($employee->profile_photo_path)
                                                    <img src="{{ Storage::url($employee->profile_photo_path) }}" alt="{{ $employee->name }}" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <span class="text-gray-500 text-sm font-bold">{{ substr($employee->name, 0, 2) }}</span>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <a href="{{ route('employees.show', $employee) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $employee->name }}</a>
                                                <p class="text-xs text-gray-500">{{ $employee->position }} · {{ $employee->employee_id }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500">Tidak ada karyawan yang ditugaskan di lokasi ini</p>
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('employees.index', ['location' => $location->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    Lihat semua karyawan <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
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
            // Get coordinates
            let lat = {{ $location->latitude }};
            let lng = {{ $location->longitude }};
            let radius = {{ $location->radius }};

            // Initialize map
            const map = L.map('map').setView([lat, lng], 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker and circle
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $location->name }}</b><br>{{ $location->address }}");

            L.circle([lat, lng], {
                radius: radius,
                color: '#3b82f6',
                fillColor: '#93c5fd',
                fillOpacity: 0.3
            }).addTo(map);
        });
    </script>
    @endpush
</x-app-layout>
