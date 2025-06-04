<!-- resources/views/attendance/form.blade.php -->
<x-app-layout>
    @section('title', 'Presensi')
    @section('breadcrumb', 'Presensi')

    <div class="max-w-2xl mx-auto" x-data="attendanceForm()">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">Sistem Presensi</h1>
                <p class="mt-2 text-gray-600">{{ $employee->user->name }} • {{ $employee->employee_id }}</p>
                <p class="text-sm text-gray-500">{{ $employee->location->name }}</p>
            </div>

            <!-- Current Status -->
            <div class="mt-6 flex justify-center space-x-8">
                <div class="text-center">
                    <div class="text-sm text-gray-500">Clock In</div>
                    <div class="text-lg font-semibold {{ $todayClockIn ? ($todayClockIn->is_late ? 'text-red-600' : 'text-green-600') : 'text-gray-400' }}">
                        {{ $todayClockIn ? $todayClockIn->attendance_time->format('H:i:s') : '-' }}
                    </div>
                    @if($todayClockIn && $todayClockIn->is_late)
                        <div class="text-xs text-red-600">Terlambat {{ $todayClockIn->late_minutes }} menit</div>
                    @endif
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500">Clock Out</div>
                    <div class="text-lg font-semibold {{ $todayClockOut ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $todayClockOut ? $todayClockOut->attendance_time->format('H:i:s') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Attendance Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Action Type Selection -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    @if($canClockIn)
                        Clock In - Presensi Masuk
                    @elseif($canClockOut)
                        Clock Out - Presensi Pulang
                    @else
                        Presensi Selesai
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if($canClockIn)
                        Ambil foto selfie dan pastikan lokasi GPS aktif untuk melakukan presensi masuk
                    @elseif($canClockOut)
                        Ambil foto selfie dan pastikan lokasi GPS aktif untuk melakukan presensi pulang
                    @else
                        Anda sudah menyelesaikan presensi untuk hari ini
                    @endif
                </p>
            </div>

            @if($canClockIn || $canClockOut)
                <form @submit.prevent="submitAttendance()">
                    <div class="p-6 space-y-6">
                        <!-- Camera Section -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">📸 Ambil Foto Selfie</h3>
                                <button type="button"
                                        @click="toggleCamera()"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i x-show="!cameraActive" class="fas fa-camera mr-2"></i>
                                    <i x-show="cameraActive" class="fas fa-stop mr-2"></i>
                                    <span x-text="cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
                                </button>
                            </div>

                            <!-- Camera/Photo Display -->
                            <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                                <!-- Video Stream -->
                                <video x-ref="video"
                                       x-show="cameraActive && !capturedPhoto"
                                       autoplay
                                       muted
                                       class="w-full h-full object-cover"></video>

                                <!-- Captured Photo -->
                                <img x-show="capturedPhoto"
                                     x-bind:src="capturedPhoto"
                                     class="w-full h-full object-cover"
                                     alt="Captured photo">

                                <!-- Placeholder -->
                                <div x-show="!cameraActive && !capturedPhoto"
                                     class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-camera text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500">Tekan tombol untuk mengaktifkan kamera</p>
                                    </div>
                                </div>

                                <!-- Camera Overlay -->
                                <div x-show="cameraActive"
                                     class="absolute inset-0 pointer-events-none">
                                    <!-- Face detection guide -->
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 border-2 border-white rounded-full opacity-50"></div>
                                </div>

                                <!-- Camera Controls -->
                                <div x-show="cameraActive"
                                     class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                                    <button type="button"
                                            @click="capturePhoto()"
                                            class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors duration-200">
                                        <div class="w-12 h-12 bg-blue-600 rounded-full"></div>
                                    </button>
                                </div>

                                <!-- Loading Overlay -->
                                <div x-show="processing"
                                     class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p>Memproses foto...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo Actions -->
                            <div x-show="capturedPhoto" class="flex justify-center space-x-3">
                                <button type="button"
                                        @click="retakePhoto()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="fas fa-redo mr-2"></i>
                                    Ambil Ulang
                                </button>
                            </div>
                        </div>

                        <!-- Location Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">📍 Deteksi Lokasi</h3>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">Status GPS:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="locationStatus === 'success' ? 'bg-green-100 text-green-800' : locationStatus === 'error' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'">
                                        <i class="fas mr-1"
                                           :class="locationStatus === 'success' ? 'fa-check-circle' : locationStatus === 'error' ? 'fa-exclamation-circle' : 'fa-spinner fa-spin'"></i>
                                        <span x-text="locationStatusText"></span>
                                    </span>
                                </div>

                                <div x-show="location.latitude" class="space-y-2 text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Latitude:</span>
                                        <span x-text="location.latitude"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Longitude:</span>
                                        <span x-text="location.longitude"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Jarak dari kantor:</span>
                                        <span x-text="location.distance ? location.distance + 'm' : '-'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Status lokasi:</span>
                                        <span class="font-medium"
                                              :class="location.isValid ? 'text-green-600' : 'text-red-600'"
                                              x-text="location.isValid ? 'Dalam radius kantor' : 'Di luar radius kantor'"></span>
                                    </div>
                                </div>

                                <button type="button"
                                        @click="getCurrentLocation()"
                                        :disabled="gettingLocation"
                                        class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    <i class="fas mr-2" :class="gettingLocation ? 'fa-spinner fa-spin' : 'fa-map-marker-alt'"></i>
                                    <span x-text="gettingLocation ? 'Mendapatkan lokasi...' : 'Perbarui Lokasi'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="space-y-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                            <textarea id="notes"
                                      x-model="notes"
                                      rows="3"
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit"
                                    :disabled="!canSubmit"
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
                                    :class="canSubmit ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' : 'bg-gray-300 cursor-not-allowed'">
                                <span x-show="!submitting">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    @if($canClockIn)
                                        Submit Clock In
                                    @else
                                        Submit Clock Out
                                    @endif
                                </span>
                                <span x-show="submitting" class="flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Memproses...
                                </span>
                            </button>
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Syarat Presensi:</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <i class="fas mr-2" :class="capturedPhoto ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-300'"></i>
                                    <span :class="capturedPhoto ? 'text-green-700' : 'text-gray-600'">Foto selfie telah diambil</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas mr-2" :class="locationStatus === 'success' ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-300'"></i>
                                    <span :class="locationStatus === 'success' ? 'text-green-700' : 'text-gray-600'">Lokasi GPS terdeteksi</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas mr-2" :class="location.isValid ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-300'"></i>
                                    <span :class="location.isValid ? 'text-green-700' : 'text-gray-600'">Berada dalam radius kantor (max {{ $employee->location->radius }}m)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <!-- Completed State -->
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Presensi Hari Ini Selesai</h3>
                    <p class="text-gray-500 mb-6">Anda sudah melakukan clock in dan clock out untuk hari ini.</p>

                    <div class="space-y-3">
                        <a href="{{ route('dashboard') }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                        <a href="{{ route('attendance.history') }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-history mr-2"></i>
                            Lihat Riwayat Presensi
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function attendanceForm() {
            return {
                // Camera state
                cameraActive: false,
                capturedPhoto: null,
                videoStream: null,
                processing: false,

                // Location state
                location: {
                    latitude: null,
                    longitude: null,
                    distance: null,
                    isValid: false
                },
                locationStatus: 'pending', // 'pending', 'success', 'error'
                locationStatusText: 'Menunggu...',
                gettingLocation: false,

                // Form state
                notes: '',
                submitting: false,

                // Computed
                get canSubmit() {
                    return this.capturedPhoto &&
                           this.locationStatus === 'success' &&
                           !this.submitting;
                },

                // Initialize
                init() {
                    this.getCurrentLocation();
                },

                // Camera methods
                async toggleCamera() {
                    if (this.cameraActive) {
                        this.stopCamera();
                    } else {
                        await this.startCamera();
                    }
                },

                async startCamera() {
                    try {
                        const constraints = {
                            video: {
                                width: { ideal: 640 },
                                height: { ideal: 480 },
                                facingMode: 'user'
                            }
                        };

                        const stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.videoStream = stream;
                        this.$refs.video.srcObject = stream;
                        this.cameraActive = true;
                        this.capturedPhoto = null;
                    } catch (error) {
                        console.error('Error accessing camera:', error);
                        window.showError('Gagal mengakses kamera. Pastikan browser memiliki izin kamera.');
                    }
                },

                stopCamera() {
                    if (this.videoStream) {
                        this.videoStream.getTracks().forEach(track => track.stop());
                        this.videoStream = null;
                    }
                    this.cameraActive = false;
                },

                capturePhoto() {
                    if (!this.cameraActive) return;

                    this.processing = true;

                    setTimeout(() => {
                        const video = this.$refs.video;
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        // Set canvas dimensions
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;

                        // Draw video frame to canvas
                        context.drawImage(video, 0, 0);

                        // Convert to base64
                        this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);

                        // Stop camera
                        this.stopCamera();
                        this.processing = false;
                    }, 500);
                },

                retakePhoto() {
                    this.capturedPhoto = null;
                    this.startCamera();
                },

                // Location methods
                getCurrentLocation() {
                    if (this.gettingLocation) return;

                    this.gettingLocation = true;
                    this.locationStatus = 'pending';
                    this.locationStatusText = 'Mendapatkan lokasi...';

                    if (!navigator.geolocation) {
                        this.locationStatus = 'error';
                        this.locationStatusText = 'GPS tidak didukung';
                        this.gettingLocation = false;
                        return;
                    }

                    const options = {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    };

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.location.latitude = position.coords.latitude.toFixed(6);
                            this.location.longitude = position.coords.longitude.toFixed(6);
                            this.validateLocation();
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            this.locationStatus = 'error';

                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    this.locationStatusText = 'Izin lokasi ditolak';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    this.locationStatusText = 'Lokasi tidak tersedia';
                                    break;
                                case error.TIMEOUT:
                                    this.locationStatusText = 'Timeout mendapatkan lokasi';
                                    break;
                                default:
                                    this.locationStatusText = 'Error mendapatkan lokasi';
                                    break;
                            }
                            this.gettingLocation = false;
                        },
                        options
                    );
                },

                validateLocation() {
                    // Office location from backend
                    const officeLocation = {
                        latitude: {{ $employee->location->latitude }},
                        longitude: {{ $employee->location->longitude }},
                        radius: {{ $employee->location->radius }}
                    };

                    // Calculate distance using Haversine formula
                    const distance = this.calculateDistance(
                        this.location.latitude,
                        this.location.longitude,
                        officeLocation.latitude,
                        officeLocation.longitude
                    );

                    this.location.distance = Math.round(distance);
                    this.location.isValid = distance <= officeLocation.radius;

                    this.locationStatus = 'success';
                    this.locationStatusText = this.location.isValid ? 'Lokasi valid' : 'Di luar jangkauan';
                    this.gettingLocation = false;
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
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
                },

                // Form submission
                async submitAttendance() {
                    if (!this.canSubmit) return;

                    this.submitting = true;

                    try {
                        const attendanceType = {{ $canClockIn ? "'clock-in'" : "'clock-out'" }};
                        const endpoint = attendanceType === 'clock-in' ?
                            '{{ route("attendance.clock-in") }}' :
                            '{{ route("attendance.clock-out") }}';

                        const formData = {
                            photo: this.capturedPhoto,
                            latitude: this.location.latitude,
                            longitude: this.location.longitude,
                            notes: this.notes,
                            _token: '{{ csrf_token() }}'
                        };

                        const response = await window.axios.post(endpoint, formData);

                        if (response.data.success) {
                            window.showSuccess('Presensi berhasil!', response.data.message);

                            setTimeout(() => {
                                window.location.href = '{{ route("dashboard") }}';
                            }, 2000);
                        } else {
                            window.showError('Presensi gagal!', response.data.message);
                        }

                    } catch (error) {
                        console.error('Attendance submission error:', error);

                        let errorMessage = 'Terjadi kesalahan pada server';
                        if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }

                        window.showError('Presensi gagal!', errorMessage);
                    } finally {
                        this.submitting = false;
                    }
                },

                // Cleanup
                destroy() {
                    this.stopCamera();
                }
            }
        }

        // Cleanup camera on page unload
        window.addEventListener('beforeunload', function() {
            // Stop any active camera streams
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.enumerateDevices().then(devices => {
                    devices.forEach(device => {
                        if (device.kind === 'videoinput') {
                            // Device cleanup handled by browser
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
