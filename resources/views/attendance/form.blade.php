<x-app-layout>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Presensi
    </h2>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Employee Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-lg text-blue-900 mb-2">Informasi Karyawan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-blue-700">Nama:</span> {{ $employee->user->name }}
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">ID:</span> {{ $employee->employee_id }}
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Jabatan:</span> {{ $employee->position }}
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Lokasi:</span> {{ $employee->location->name }}
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Jam Kerja:</span>
                                {{ $workStartTime->format('H:i') }} - {{ $workEndTime->format('H:i') }}
                                @if($employee->is_flexible_time)
                                    <span class="text-green-600">(Fleksibel)</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-blue-700">Status:</span>
                                @if($isCurrentlyLate && $canClockIn)
                                    <span class="text-red-600 font-medium">Terlambat {{ $lateMinutes }} menit</span>
                                @else
                                    <span class="text-green-600">Tepat Waktu</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Today's Attendance Status -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-lg text-gray-900 mb-3">Status Presensi Hari Ini</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full {{ $todayClockIn ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                <div>
                                    <div class="font-medium">Clock In</div>
                                    @if($todayClockIn)
                                        <div class="text-sm text-gray-600">
                                            {{ $todayClockIn->attendance_time->format('H:i:s') }}
                                            @if($todayClockIn->is_late)
                                                <span class="text-red-600">(Terlambat {{ $todayClockIn->late_minutes }} menit)</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Belum clock in</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full {{ $todayClockOut ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                <div>
                                    <div class="font-medium">Clock Out</div>
                                    @if($todayClockOut)
                                        <div class="text-sm text-gray-600">
                                            {{ $todayClockOut->attendance_time->format('H:i:s') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Belum clock out</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Form -->
                    <div x-data="attendanceForm()" x-init="init()" class="space-y-6">

                        <!-- Camera Section -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-lg mb-4">Ambil Foto Selfie</h3>

                            <div class="relative">
                                <!-- Video Preview -->
                                <video
                                    x-ref="video"
                                    class="w-full max-w-md mx-auto rounded-lg bg-gray-100"
                                    x-show="!photoTaken && cameraReady"
                                    autoplay
                                    playsinline
                                ></video>

                                <!-- Photo Preview -->
                                <img
                                    x-ref="photoPreview"
                                    x-show="photoTaken"
                                    class="w-full max-w-md mx-auto rounded-lg"
                                    style="display: none;"
                                >

                                <!-- Camera Loading -->
                                <div x-show="!cameraReady && !cameraError" class="w-full max-w-md mx-auto h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl mb-2"></i>
                                        <p class="text-gray-600">Mengaktifkan kamera...</p>
                                    </div>
                                </div>

                                <!-- Camera Error -->
                                <div x-show="cameraError" class="w-full max-w-md mx-auto h-64 bg-red-50 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-2"></i>
                                        <p class="text-red-600 font-medium">Gagal mengakses kamera</p>
                                        <p class="text-sm text-red-500 mt-1" x-text="cameraErrorMessage"></p>
                                        <button
                                            @click="initCamera()"
                                            class="mt-2 bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700"
                                        >
                                            Coba Lagi
                                        </button>
                                    </div>
                                </div>

                                <!-- Canvas for photo capture (hidden) -->
                                <canvas x-ref="canvas" style="display: none;"></canvas>
                            </div>

                            <!-- Camera Controls -->
                            <div class="flex justify-center space-x-4 mt-4">
                                <button
                                    @click="takePhoto()"
                                    x-show="cameraReady && !photoTaken"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2"
                                >
                                    <i class="fas fa-camera"></i>
                                    <span>Ambil Foto</span>
                                </button>

                                <button
                                    @click="retakePhoto()"
                                    x-show="photoTaken"
                                    class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2"
                                >
                                    <i class="fas fa-redo"></i>
                                    <span>Ambil Ulang</span>
                                </button>
                            </div>
                        </div>

                        <!-- Location Section -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-lg mb-4">Lokasi GPS</h3>

                            <div x-show="!locationReady && !locationError" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin text-blue-600 text-2xl mb-2"></i>
                                <p class="text-gray-600">Mendapatkan lokasi GPS...</p>
                            </div>

                            <div x-show="locationError" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-red-800 font-medium">Gagal mendapatkan lokasi GPS</p>
                                        <p class="text-red-600 text-sm mt-1" x-text="locationErrorMessage"></p>
                                        <button
                                            @click="getLocation()"
                                            class="mt-2 bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700"
                                        >
                                            Coba Lagi
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="locationReady" class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-green-400 mr-2 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-green-800 font-medium">Lokasi GPS berhasil didapatkan</p>
                                        <div class="text-green-700 text-sm mt-1">
                                            <p>Latitude: <span x-text="latitude"></span></p>
                                            <p>Longitude: <span x-text="longitude"></span></p>
                                            <p>Akurasi: <span x-text="accuracy"></span> meter</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="border rounded-lg p-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea
                                x-model="notes"
                                id="notes"
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tambahkan catatan jika diperlukan..."
                                maxlength="500"
                            ></textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                <span x-text="notes.length"></span>/500 karakter
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if($canClockIn)
                                <button
                                    @click="submitAttendance('clock_in')"
                                    :disabled="!canSubmit || processing"
                                    class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                >
                                    <i x-show="!processing" class="fas fa-sign-in-alt"></i>
                                    <i x-show="processing" class="fas fa-spinner fa-spin"></i>
                                    <span x-text="processing ? 'Memproses...' : 'Clock In'"></span>
                                </button>
                            @endif

                            @if($canClockOut)
                                <button
                                    @click="submitAttendance('clock_out')"
                                    :disabled="!canSubmit || processing"
                                    class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                >
                                    <i x-show="!processing" class="fas fa-sign-out-alt"></i>
                                    <i x-show="processing" class="fas fa-spinner fa-spin"></i>
                                    <span x-text="processing ? 'Memproses...' : 'Clock Out'"></span>
                                </button>
                            @endif

                            @if(!$canClockIn && !$canClockOut)
                                <div class="text-center py-4">
                                    <p class="text-gray-600">Presensi hari ini sudah selesai</p>
                                    <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                        Lihat Riwayat Presensi
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Checklist Presensi</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center space-x-2">
                                    <div :class="cameraReady ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="cameraReady ? 'text-green-700' : 'text-gray-600'">Kamera siap</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div :class="photoTaken ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="photoTaken ? 'text-green-700' : 'text-gray-600'">Foto selfie diambil</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div :class="locationReady ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="locationReady ? 'text-green-700' : 'text-gray-600'">Lokasi GPS didapatkan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div x-data="{ show: false, title: '', message: '', type: 'success' }"
         x-show="show"
         x-cloak
         @attendance-result.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div :class="type === 'success' ? 'bg-green-100' : 'bg-red-100'" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i x-show="type === 'success'" class="fas fa-check text-green-600"></i>
                            <i x-show="type === 'error'" class="fas fa-times text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="show = false; if(type === 'success') location.reload()"
                            type="button"
                            :class="type === 'success' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function attendanceForm() {
            return {
                // Camera
                cameraReady: false,
                cameraError: false,
                cameraErrorMessage: '',
                photoTaken: false,
                photoData: null,
                stream: null,

                // Location
                locationReady: false,
                locationError: false,
                locationErrorMessage: '',
                latitude: null,
                longitude: null,
                accuracy: null,

                // Form
                notes: '',
                processing: false,

                get canSubmit() {
                    return this.photoTaken && this.locationReady && !this.processing;
                },

                async init() {
                    await this.initCamera();
                    await this.getLocation();
                },

                async initCamera() {
                    this.cameraError = false;
                    this.cameraErrorMessage = '';

                    try {
                        // Stop existing stream if any
                        if (this.stream) {
                            this.stream.getTracks().forEach(track => track.stop());
                        }

                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                width: { ideal: 640 },
                                height: { ideal: 480 },
                                facingMode: 'user'
                            },
                            audio: false
                        });

                        this.$refs.video.srcObject = this.stream;
                        this.cameraReady = true;
                    } catch (error) {
                        console.error('Camera error:', error);
                        this.cameraError = true;

                        if (error.name === 'NotAllowedError') {
                            this.cameraErrorMessage = 'Izin akses kamera ditolak. Silakan berikan izin dan refresh halaman.';
                        } else if (error.name === 'NotFoundError') {
                            this.cameraErrorMessage = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
                        } else if (error.name === 'NotSupportedError') {
                            this.cameraErrorMessage = 'Browser tidak mendukung akses kamera.';
                        } else {
                            this.cameraErrorMessage = 'Gagal mengakses kamera: ' + error.message;
                        }
                    }
                },

                takePhoto() {
                    if (!this.cameraReady) return;

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const context = canvas.getContext('2d');

                    // Set canvas size to video size
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Draw video frame to canvas
                    context.drawImage(video, 0, 0);

                    // Get base64 image data
                    this.photoData = canvas.toDataURL('image/jpeg', 0.8);

                    // Show preview
                    this.$refs.photoPreview.src = this.photoData;
                    this.photoTaken = true;

                    // Stop camera stream
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.cameraReady = false;
                    }
                },

                retakePhoto() {
                    this.photoTaken = false;
                    this.photoData = null;
                    this.initCamera();
                },

                async getLocation() {
                    this.locationError = false;
                    this.locationErrorMessage = '';

                    if (!navigator.geolocation) {
                        this.locationError = true;
                        this.locationErrorMessage = 'Browser tidak mendukung GPS.';
                        return;
                    }

                    const options = {
                        enableHighAccuracy: true,
                        timeout: 60000, // Meningkatkan timeout dari 30000 menjadi 60000 ms
                        maximumAge: 0 // Mengurangi maximumAge untuk mendapatkan posisi terbaru
                    };

                    try {
                        // Tambahkan indikator loading
                        this.locationErrorMessage = 'Sedang mencari lokasi GPS...';

                        // Coba reset izin geolocation dengan meminta ulang
                        if (navigator.permissions && navigator.permissions.query) {
                            try {
                                const permissionStatus = await navigator.permissions.query({ name: 'geolocation' });
                                if (permissionStatus.state === 'denied') {
                                    // Jika izin ditolak di level browser
                                    this.locationError = true;
                                    this.locationErrorMessage = 'Izin lokasi ditolak di browser. Silakan buka pengaturan browser dan izinkan akses lokasi untuk website ini, lalu refresh halaman.';
                                    return;
                                }
                            } catch (permErr) {
                                console.log('Permission check error:', permErr);
                                // Lanjutkan meskipun pemeriksaan izin gagal
                            }
                        }

                        const position = await new Promise((resolve, reject) => {
                            // Gunakan watchPosition untuk mendapatkan pembaruan lokasi berkelanjutan
                            const watchId = navigator.geolocation.watchPosition(
                                (pos) => {
                                    // Hentikan watch setelah mendapatkan posisi yang akurat
                                    if (pos.coords.accuracy < 100) {
                                        navigator.geolocation.clearWatch(watchId);
                                        resolve(pos);
                                    }
                                },
                                (err) => {
                                    // Tangani error khusus watchPosition
                                    navigator.geolocation.clearWatch(watchId);
                                    reject(err);
                                },
                                options
                            );

                            // Fallback ke getCurrentPosition jika watchPosition tidak mendapatkan hasil akurat
                            setTimeout(() => {
                                navigator.geolocation.getCurrentPosition(resolve, reject, options);
                            }, 2000); // Tunggu 2 detik sebelum mencoba getCurrentPosition

                            // Set timeout untuk membersihkan watch jika terlalu lama
                            setTimeout(() => {
                                navigator.geolocation.clearWatch(watchId);
                            }, options.timeout - 5000); // Bersihkan 5 detik sebelum timeout utama
                        });

                        this.latitude = position.coords.latitude;
                        this.longitude = position.coords.longitude;
                        this.accuracy = Math.round(position.coords.accuracy);
                        this.locationReady = true;
                        this.locationErrorMessage = '';
                    } catch (error) {
                        console.error('Location error:', error);
                        this.locationError = true;

                        switch (error.code) {
                            case 1: // PERMISSION_DENIED
                                this.locationErrorMessage = 'Izin akses lokasi ditolak. Silakan buka pengaturan browser, izinkan akses lokasi untuk website ini, lalu refresh halaman.';
                                break;
                            case 2: // POSITION_UNAVAILABLE
                                this.locationErrorMessage = 'Lokasi tidak tersedia. Pastikan GPS aktif dan coba refresh halaman. Jika menggunakan WiFi, coba gunakan koneksi data seluler.';
                                break;
                            case 3: // TIMEOUT
                                this.locationErrorMessage = 'Timeout mendapatkan lokasi. Pastikan GPS aktif dan coba lagi. Jika berada di dalam ruangan, coba pindah ke dekat jendela atau area terbuka.';
                                break;
                            default:
                                this.locationErrorMessage = 'Gagal mendapatkan lokasi: ' + (error.message || 'Error tidak diketahui') + '. Coba refresh halaman atau gunakan perangkat lain.';
                                break;
                        }
                    }
                },

                async submitAttendance(type) {
                    if (!this.canSubmit) return;

                    this.processing = true;

                    try {
                        const response = await fetch(`/attendance/${type === 'clock_in' ? 'clock-in' : 'clock-out'}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                photo: this.photoData,
                                latitude: this.latitude,
                                longitude: this.longitude,
                                notes: this.notes
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            let message = result.message;
                            if (result.data) {
                                message += `\n\nDetail:\n`;
                                message += `- Waktu: ${result.data.time}\n`;
                                message += `- Lokasi: ${result.data.location_valid ? 'Valid' : 'Tidak Valid'} (${result.data.distance}m)\n`;
                                message += `- Wajah: ${result.data.face_verified ? 'Terverifikasi' : 'Tidak Terverifikasi'} (${result.data.similarity_score}%)\n`;
                                if (result.data.is_late) {
                                    message += `- Status: Terlambat ${result.data.late_minutes} menit\n`;
                                }
                            }

                            this.$dispatch('attendance-result', {
                                type: 'success',
                                title: 'Presensi Berhasil!',
                                message: message
                            });
                        } else {
                            this.$dispatch('attendance-result', {
                                type: 'error',
                                title: 'Presensi Gagal!',
                                message: result.message
                            });
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        this.$dispatch('attendance-result', {
                            type: 'error',
                            title: 'Error!',
                            message: 'Terjadi kesalahan jaringan. Silakan coba lagi.'
                        });
                    } finally {
                        this.processing = false;
                    }
                },

                // Cleanup when component is destroyed
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
