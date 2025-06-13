<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Lokasi Kantor</h3>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('locations.create') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Lokasi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Lokasi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Alamat
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Koordinat
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Radius
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Karyawan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($locations as $location)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $location->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>{{ $location->timezone }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                                {{ $location->address ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $location->radius }} m
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-center text-gray-900">
                                                {{ $location->employees_count }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span data-location-id="{{ $location->id }}" class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <i class="fas fa-circle mr-1 text-xs {{ $location->is_active ? 'text-green-400' : 'text-red-400' }}"></i>
                                                {{ $location->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('locations.show', $location) }}" class="text-indigo-600 hover:text-indigo-900" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('locations.edit', $location) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="toggle-status text-green-600 hover:text-green-900"
                                                    data-location-id="{{ $location->id }}"
                                                    data-location-name="{{ $location->name }}"
                                                    data-is-active="{{ $location->is_active ? 1 : 0 }}"
                                                    title="{{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $location->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                                @if($location->employees_count == 0)
                                                    <button type="button" class="delete-location text-red-600 hover:text-red-900"
                                                        data-location-id="{{ $location->id }}"
                                                        data-location-name="{{ $location->name }}"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="text-gray-400 cursor-not-allowed" title="Tidak dapat menghapus lokasi yang memiliki karyawan">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            <i class="fas fa-map-marked mr-2"></i>Tidak ada data lokasi. <a href="{{ route('locations.create') }}" class="text-indigo-600 hover:text-indigo-900">Tambah lokasi baru</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Hapus Lokasi
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus lokasi <span id="locationNameToDelete" class="font-semibold"></span>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                    <button type="button" id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Status Modal -->
    <div id="toggleStatusModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Ubah Status Lokasi
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="toggleStatusMessage">
                                    Apakah Anda yakin ingin mengubah status lokasi <span id="locationNameToToggle" class="font-semibold"></span>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmToggleStatus" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-exchange-alt mr-2"></i>Ubah Status
                    </button>
                    <button type="button" id="cancelToggleStatus" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete location
            const deleteModal = document.getElementById('deleteModal');
            const locationNameToDelete = document.getElementById('locationNameToDelete');
            const deleteForm = document.getElementById('deleteForm');
            const cancelDelete = document.getElementById('cancelDelete');

            document.querySelectorAll('.delete-location').forEach(button => {
                button.addEventListener('click', function() {
                    const locationId = this.dataset.locationId;
                    const locationName = this.dataset.locationName;

                    locationNameToDelete.textContent = locationName;
                    deleteForm.action = `/locations/${locationId}`;
                    deleteModal.classList.remove('hidden');
                });
            });

            cancelDelete.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
            });

            // Toggle status
            const toggleStatusModal = document.getElementById('toggleStatusModal');
            const locationNameToToggle = document.getElementById('locationNameToToggle');
            const toggleStatusMessage = document.getElementById('toggleStatusMessage');
            const confirmToggleStatus = document.getElementById('confirmToggleStatus');
            const cancelToggleStatus = document.getElementById('cancelToggleStatus');
            let currentLocationId, currentIsActive;

            document.querySelectorAll('.toggle-status').forEach(button => {
                button.addEventListener('click', function() {
                    currentLocationId = this.dataset.locationId;
                    currentIsActive = this.dataset.isActive === '1';
                    const locationName = this.dataset.locationName;

                    locationNameToToggle.textContent = locationName;
                    toggleStatusMessage.innerHTML = `Apakah Anda yakin ingin ${currentIsActive ? 'menonaktifkan' : 'mengaktifkan'} lokasi <span class="font-semibold">${locationName}</span>?`;
                    confirmToggleStatus.textContent = currentIsActive ? 'Nonaktifkan' : 'Aktifkan';
                    confirmToggleStatus.classList.remove('bg-yellow-600', 'hover:bg-yellow-700', 'bg-green-600', 'hover:bg-green-700');

                    if (currentIsActive) {
                        confirmToggleStatus.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                    } else {
                        confirmToggleStatus.classList.add('bg-green-600', 'hover:bg-green-700');
                    }

                    toggleStatusModal.classList.remove('hidden');
                });
            });

            cancelToggleStatus.addEventListener('click', function() {
                toggleStatusModal.classList.add('hidden');
            });

            confirmToggleStatus.addEventListener('click', function() {
                // Send AJAX request to toggle status
                fetch(`/locations/${currentLocationId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const statusBadge = document.querySelector(`.status-badge[data-location-id="${currentLocationId}"]`);
                        const toggleButton = document.querySelector(`.toggle-status[data-location-id="${currentLocationId}"]`);

                        if (data.is_active) {
                            statusBadge.classList.remove('bg-red-100', 'text-red-800');
                            statusBadge.classList.add('bg-green-100', 'text-green-800');
                            statusBadge.innerHTML = `
                                <i class="fas fa-circle mr-1 text-xs text-green-400"></i>
                                Aktif
                            `;
                            toggleButton.title = 'Nonaktifkan';
                            toggleButton.querySelector('i').classList.remove('fa-toggle-off');
                            toggleButton.querySelector('i').classList.add('fa-toggle-on');
                            toggleButton.dataset.isActive = '1';
                        } else {
                            statusBadge.classList.remove('bg-green-100', 'text-green-800');
                            statusBadge.classList.add('bg-red-100', 'text-red-800');
                            statusBadge.innerHTML = `
                                <i class="fas fa-circle mr-1 text-xs text-red-400"></i>
                                Tidak Aktif
                            `;
                            toggleButton.title = 'Aktifkan';
                            toggleButton.querySelector('i').classList.remove('fa-toggle-on');
                            toggleButton.querySelector('i').classList.add('fa-toggle-off');
                            toggleButton.dataset.isActive = '0';
                        }

                        window.showNotification(data.message, 'success');
                    } else {
                        window.showNotification(data.message, 'error');
                    }

                    toggleStatusModal.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showNotification('Terjadi kesalahan saat mengubah status lokasi', 'error');
                    toggleStatusModal.classList.add('hidden');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
