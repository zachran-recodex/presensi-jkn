<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-camera mr-2 text-blue-600"></i>
                    Presensi Karyawan
                </h2>
                <p class="text-sm text-gray-600 mt-1">Ambil foto selfie dan pastikan lokasi GPS aktif</p>
            </div>
            <div class="text-sm text-gray-600">
                <i class="fas fa-user mr-1"></i>
                {{ $employee->user->name }} ({{ $employee->employee_id }})
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Clock In Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Clock In</h3>
                        <p class="text-sm text-gray-600">Presensi masuk kerja</p>
                    </div>
                    <div class="text-right">
                        @if($todayClockIn)
                            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-green-600 mt-2">
                                {{ $todayClockIn->attendance_time->format('H:i') }}
                            </p>
                            @if($todayClockIn->is_late)
                                <p class="text-xs text-red-600">Terlambat {{ $todayClockIn->late_minutes }}m</p>
                            @endif
                        @else
                            <div class="bg-gray-100 text-gray-400 p-3 rounded-full">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Belum clock in</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Clock Out Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Clock Out</h3>
                        <p class="text-sm text-gray-600">Presensi pulang kerja</p>
                    </div>
                    <div class="text-right">
                        @if($todayClockOut)
                            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-blue-600 mt-2">
                                {{ $todayClockOut->attendance_time->format('H:i') }}
                            </p>
                        @else
                            <div class="bg-gray-100 text-gray-400 p-3 rounded-full">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Belum clock out</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Attendance Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200"
             x-data="attendanceForm({{ $canClockIn ? 'true' : 'false' }}, {{ $canClockOut ? 'true' : 'false' }})">

            <!-- Camera Section -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-camera mr-2 text-blue-600"></i>
                    Ambil Foto Selfie
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Camera Preview -->
                    <div class="space-y-4">
                        <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                            <video x-ref="video"
                                   autoplay
                                   muted
                                   playsinline
                                   class="w-full h-full object-cover">
                            </video>

                            <!-- Camera overlay -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute inset-4 border-2 border-white border-dashed rounded-lg opacity-50"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <div class="w-32 h-40 border-2 border-white rounded-lg opacity-70"></div>
                                </div>
                            </div>

                            <!-- Camera status -->
                            <div x-show="!cameraReady" class="absolute inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <i class="fas fa-camera text-4xl mb-2"></i>
                                    <p>Mengaktifkan kamera...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Camera Controls -->
                        <div class="flex justify-center space-x-4">
                            <button @click="startCamera()"
                                    x-show="!cameraReady"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-video mr-2"></i>
                                Aktifkan Kamera
                            </button>

                            <button @click="capturePhoto()"
                                    x-show="cameraReady && !photoTaken"
                                    :disabled="!canTakePhoto"
                                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400">
                                <i class="fas fa-camera mr-2"></i>
                                Ambil Foto
                            </button>

                            <button @click="retakePhoto()"
                                    x-show="photoTaken"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-redo mr-2"></i>
                                Foto Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Captured Photo -->
                    <div class="space-y-4">
                        <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                            <canvas x-ref="canvas"
                                    x-show="photoTaken"
                                    class="w-full h-full object-cover">
                            </canvas>

                            <div x-show="!photoTaken" class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-image text-4xl mb-2"></i>
                                    <p>Foto akan tampil di sini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Status -->
                        <div x-show="photoTaken" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-green-800 font-medium">Foto berhasil diambil</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Section -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                    Verifikasi Lokasi
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Lokasi Kantor</h4>
                            <p class="text-sm text-gray-600">{{ $employee->location->name }}</p>
                            <p class="text-sm text-gray-600">{{ $employee->location->address }}</p>
                            <p class="text-xs text-gray-500 mt-2">
                                Radius: {{ $employee->location->radius }} meter
                            </p>
                        </div>

                        <button @click="getLocation()"
                                :disabled="locationChecking"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-400">
                            <i class="fas fa-crosshairs mr-2"></i>
                            <span x-text="locationChecking ? 'Mengecek Lokasi...' : 'Cek Lokasi Saya'"></span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Location Status -->
                        <div x-show="locationChecked">
                            <div x-show="locationValid" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium">Lokasi Valid</span>
                                </div>
                                <p class="text-sm text-green-700">
                                    Anda berada dalam radius kantor
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    Jarak: <span x-text="distance"></span> meter
                                </p>
                            </div>

                            <div x-show="!locationValid" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                    <span class="text-red-800 font-medium">Lokasi Tidak Valid</span>
                                </div>
                                <p class="text-sm text-red-700">
                                    Anda berada di luar radius kantor
                                </p>
                                <p class="text-xs text-red-600 mt-1">
                                    Jarak: <span x-text="distance"></span> meter (max: {{ $employee->location->radius }}m)
                                </p>
                            </div>
                        </div>

                        <!-- GPS Coordinates -->
                        <div x-show="userLocation.lat" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h5 class="font-medium text-blue-900 mb-2">Koordinat GPS</h5>
                            <p class="text-sm text-blue-700">
                                Lat: <span x-text="userLocation.lat"></span>
                            </p>
                            <p class="text-sm text-blue-700">
                                Lng: <span x-text="userLocation.lng"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form @submit.prevent="submitAttendance()">
                    <!-- Attendance Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Presensi
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio"
                                       x-model="attendanceType"
                                       value="clock_in"
                                       :disabled="!canClockIn"
                                       class="sr-only">
                                <div :class="attendanceType === 'clock_in' ? 'ring-2 ring-green-500 bg-green-50' : 'bg-gray-50'"
                                     class="p-4 rounded-lg border cursor-pointer transition-all"
                                     :class="!canClockIn ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-50'">
                                    <div class="flex items-center">
                                        <i class="fas fa-sign-in-alt text-green-600 mr-3 text-xl"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Clock In</p>
                                            <p class="text-sm text-gray-600">Presensi masuk kerja</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio"
                                       x-model="attendanceType"
                                       value="clock_out"
                                       :disabled="!canClockOut"
                                       class="sr-only">
                                <div :class="attendanceType === 'clock_out' ? 'ring-2 ring-blue-500 bg-blue-50' : 'bg-gray-50'"
                                     class="p-4 rounded-lg border cursor-pointer transition-all"
                                     :class="!canClockOut ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-50'">
                                    <div class="flex items-center">
                                        <i class="fas fa-sign-out-alt text-blue-600 mr-3 text-xl"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Clock Out</p>
                                            <p class="text-sm text-gray-600">Presensi pulang kerja</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea x-model="notes"
                                  id="notes"
                                  rows="3"
                                  maxlength="500"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="notes.length"></span>/500 karakter
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                :disabled="!canSubmit"
                                class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-3"></i>
                            <span x-text="attendanceType === 'clock_in' ? 'Submit Clock In' : 'Submit Clock Out'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function attendanceForm(canClockIn, canClockOut) {
            return {
                // Camera properties
                cameraReady: false,
                photoTaken: false,
                photoData: null,
                stream: null,

                // Location properties
                locationChecking: false,
                locationChecked: false,
                locationValid: false,
                userLocation: {
                    lat: null,
                    lng: null
                },
                distance: 0,

                // Form properties
                attendanceType: canClockIn ? 'clock_in' : (canClockOut ? 'clock_out' : ''),
                notes: '',
                submitting: false,

                // Computed properties
                get canTakePhoto() {
                    return this.cameraReady && !this.submitting;
                },

                get canSubmit() {
                    return this.photoTaken &&
                           this.locationChecked &&
                           this.locationValid &&
                           this.attendanceType &&
                           !this.submitting;
                },

                // Initialize
                init() {
                    this.startCamera();
                },

                // Camera methods
                async startCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                width: { ideal: 640 },
                                height: { ideal: 480 },
                                facingMode: 'user'
                            }
                        });

                        this.$refs.video.srcObject = this.stream;
                        this.cameraReady = true;

                        // Auto get location when camera starts
                        setTimeout(() => this.getLocation(), 1000);

                    } catch (error) {
                        console.error('Camera error:', error);
                        alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
                    }
                },

                capturePhoto() {
                    if (!this.cameraReady) return;

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const ctx = canvas.getContext('2d');

                    // Set canvas size
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Draw video frame to canvas
                    ctx.drawImage(video, 0, 0);

                    // Get base64 data
                    this.photoData = canvas.toDataURL('image/jpeg', 0.8);
                    this.photoTaken = true;

                    // Stop camera
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.cameraReady = false;
                    }
                },

                retakePhoto() {
                    this.photoTaken = false;
                    this.photoData = null;
                    this.startCamera();
                },

                // Location methods
                async getLocation() {
                    if (!navigator.geolocation) {
                        alert('Browser tidak mendukung GPS');
                        return;
                    }

                    this.locationChecking = true;

                    try {
                        const position = await new Promise((resolve, reject) => {
                            navigator.geolocation.getCurrentPosition(resolve, reject, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 300000
                            });
                        });

                        this.userLocation = {
                            lat: position.coords.latitude.toFixed(6),
                            lng: position.coords.longitude.toFixed(6)
                        };

                        // Calculate distance from office
                        const officeLocation = {
                            lat: {{ $employee->location->latitude }},
                            lng: {{ $employee->location->longitude }}
                        };

                        this.distance = this.calculateDistance(
                            this.userLocation.lat,
                            this.userLocation.lng,
                            officeLocation.lat,
                            officeLocation.lng
                        );

                        this.locationValid = this.distance <= {{ $employee->location->radius }};
                        this.locationChecked = true;

                    } catch (error) {
                        console.error('Location error:', error);
                        alert('Tidak dapat mengakses lokasi GPS. Pastikan izin lokasi sudah diberikan.');
                    } finally {
                        this.locationChecking = false;
                    }
                },

                calculateDistance(lat1, lng1, lat2, lng2) {
                    const R = 6371e3; // Earth's radius in meters
                    const φ1 = lat1 * Math.PI/180;
                    const φ2 = lat2 * Math.PI/180;
                    const Δφ = (lat2-lat1) * Math.PI/180;
                    const Δλ = (lng2-lng1) * Math.PI/180;

                    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                            Math.cos(φ1) * Math.cos(φ2) *
                            Math.sin(Δλ/2) * Math.sin(Δλ/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                    return Math.round(R * c);
                },

                // Form submission
                async submitAttendance() {
                    if (!this.canSubmit) return;

                    this.submitting = true;
                    showLoading();

                    try {
                        const formData = {
                            photo: this.photoData,
                            latitude: this.userLocation.lat,
                            longitude: this.userLocation.lng,
                            notes: this.notes
                        };

                        const url = this.attendanceType === 'clock_in'
                            ? '{{ route("attendance.clock-in") }}'
                            : '{{ route("attendance.clock-out") }}';

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(formData)
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Show success message and redirect
                            alert('Presensi berhasil disimpan!');
                            window.location.href = '{{ route("dashboard") }}';
                        } else {
                            alert('Presensi gagal: ' + result.message);
                        }

                    } catch (error) {
                        console.error('Submit error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    } finally {
                        this.submitting = false;
                        hideLoading();
                    }
                },

                // Cleanup
                destroy() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
