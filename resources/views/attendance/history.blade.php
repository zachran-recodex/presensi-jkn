<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-history mr-2 text-purple-600"></i>
                    History Presensi
                </h2>
                <p class="text-sm text-gray-600 mt-1">Riwayat presensi Anda</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('attendance.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-camera mr-2"></i>
                    Presensi Sekarang
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('attendance.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        Dari Tanggal
                    </label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        Sampai Tanggal
                    </label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('attendance.history') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Riwayat Presensi
                    </h3>
                    <div class="text-sm text-gray-600">
                        {{ $attendances->total() }} record ditemukan
                    </div>
                </div>
            </div>

            @if($attendances->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($attendances as $attendance)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <!-- Left side - Date and Type -->
                                <div class="flex items-center space-x-4">
                                    <div class="text-center min-w-0">
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ $attendance->attendance_date->format('d') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $attendance->attendance_date->format('M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $attendance->attendance_date->isoFormat('ddd') }}
                                        </div>
                                    </div>

                                    <div class="bg-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-100 text-{{ $attendance->type == 'clock_in' ? 'green' : 'blue' }}-600 p-3 rounded-full">
                                        <i class="fas fa-{{ $attendance->type == 'clock_in' ? 'sign-in-alt' : 'sign-out-alt' }}"></i>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            {{ $attendance->type == 'clock_in' ? 'Clock In' : 'Clock Out' }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            {{ $attendance->attendance_date->isoFormat('dddd, D MMMM YYYY') }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $attendance->location->name ?? 'Unknown Location' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Center - Time and Status -->
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ $attendance->attendance_time->format('H:i') }}
                                    </div>

                                    <!-- Status Badge -->
                                    @if($attendance->status == 'success')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 mt-2">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Berhasil
                                        </span>
                                    @elseif($attendance->status == 'failed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800 mt-2">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800 mt-2">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @endif

                                    <!-- Late Status -->
                                    @if($attendance->is_late && $attendance->type == 'clock_in')
                                        <div class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Terlambat {{ $attendance->late_minutes }} menit
                                        </div>
                                    @elseif($attendance->type == 'clock_in')
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check mr-1"></i>
                                            Tepat waktu
                                        </div>
                                    @endif
                                </div>

                                <!-- Right side - Details and Actions -->
                                <div class="text-right" x-data="{ showDetails: false }">
                                    <button @click="showDetails = !showDetails"
                                            class="inline-flex items-center px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Detail
                                        <i class="fas fa-chevron-down ml-1 transform transition-transform"
                                           :class="showDetails ? 'rotate-180' : ''"></i>
                                    </button>

                                    <!-- Details Dropdown -->
                                    <div x-show="showDetails"
                                         x-transition
                                         @click.away="showDetails = false"
                                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-10">

                                        <div class="space-y-3">
                                            <!-- Photo -->
                                            @if($attendance->photo_path)
                                                <div>
                                                    <label class="text-xs font-medium text-gray-700">Foto Presensi:</label>
                                                    <img src="{{ Storage::url($attendance->photo_path) }}"
                                                         alt="Foto Presensi"
                                                         class="w-full h-32 object-cover rounded-lg mt-1 border border-gray-200">
                                                </div>
                                            @endif

                                            <!-- Location Info -->
                                            <div>
                                                <label class="text-xs font-medium text-gray-700">Lokasi GPS:</label>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $attendance->latitude }}, {{ $attendance->longitude }}
                                                </p>
                                                @if($attendance->distance_from_office)
                                                    <p class="text-xs text-gray-500">
                                                        Jarak: {{ number_format($attendance->distance_from_office, 0) }} meter
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- Face Recognition Score -->
                                            @if($attendance->face_similarity_score)
                                                <div>
                                                    <label class="text-xs font-medium text-gray-700">Face Recognition:</label>
                                                    <div class="flex items-center mt-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                            <div class="bg-{{ $attendance->face_similarity_score >= 0.75 ? 'green' : 'red' }}-600 h-2 rounded-full"
                                                                 style="width: {{ $attendance->face_similarity_score * 100 }}%"></div>
                                                        </div>
                                                        <span class="text-sm text-gray-600">
                                                            {{ number_format($attendance->face_similarity_score * 100, 1) }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Notes -->
                                            @if($attendance->notes)
                                                <div>
                                                    <label class="text-xs font-medium text-gray-700">Catatan:</label>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $attendance->notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Failure Reason -->
                                            @if($attendance->failure_reason)
                                                <div>
                                                    <label class="text-xs font-medium text-gray-700">Alasan Gagal:</label>
                                                    <p class="text-sm text-red-600 mt-1">{{ $attendance->failure_reason }}</p>
                                                </div>
                                            @endif

                                            <!-- Device Info -->
                                            <div>
                                                <label class="text-xs font-medium text-gray-700">Device Info:</label>
                                                <p class="text-xs text-gray-500 mt-1">{{ $attendance->device_info }}</p>
                                                <p class="text-xs text-gray-500">IP: {{ $attendance->ip_address }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Info Bar -->
                            @if($attendance->notes || $attendance->failure_reason)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    @if($attendance->notes)
                                        <div class="flex items-start space-x-2">
                                            <i class="fas fa-sticky-note text-blue-500 text-sm mt-0.5"></i>
                                            <p class="text-sm text-gray-600">{{ $attendance->notes }}</p>
                                        </div>
                                    @endif

                                    @if($attendance->failure_reason)
                                        <div class="flex items-start space-x-2 mt-2">
                                            <i class="fas fa-exclamation-triangle text-red-500 text-sm mt-0.5"></i>
                                            <p class="text-sm text-red-600">{{ $attendance->failure_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $attendances->withQueryString()->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada History</h3>
                    <p class="text-gray-600 mb-6">Anda belum memiliki riwayat presensi untuk periode ini.</p>
                    <a href="{{ route('attendance.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-camera mr-2"></i>
                        Lakukan Presensi Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Statistics Summary -->
        @if($attendances->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Presensi</p>
                            <p class="text-xl font-bold text-gray-900">{{ $attendances->where('status', 'success')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Clock In</p>
                            <p class="text-xl font-bold text-gray-900">
                                {{ $attendances->where('type', 'clock_in')->where('status', 'success')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 text-purple-600 p-3 rounded-lg mr-4">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Clock Out</p>
                            <p class="text-xl font-bold text-gray-900">
                                {{ $attendances->where('type', 'clock_out')->where('status', 'success')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg mr-4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Terlambat</p>
                            <p class="text-xl font-bold text-gray-900">
                                {{ $attendances->where('is_late', true)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Set default date range to current month
        document.addEventListener('DOMContentLoaded', function() {
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');

            if (!dateFromInput.value && !dateToInput.value) {
                const today = new Date();
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                dateFromInput.value = firstDay.toISOString().split('T')[0];
                dateToInput.value = lastDay.toISOString().split('T')[0];
            }
        });

        // Photo modal for better viewing
        function showPhotoModal(imageSrc) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="max-w-4xl max-h-full p-4">
                    <img src="${imageSrc}" class="max-w-full max-h-full object-contain rounded-lg">
                    <button onclick="this.parentElement.parentElement.remove()"
                            class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });

            document.body.appendChild(modal);
        }
    </script>
    @endpush
</x-app-layout>
