<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-camera mr-2 text-purple-600"></i>
                    Face Enrollment - {{ $employee->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Daftarkan wajah karyawan untuk sistem presensi</p>
            </div>
            <div>
                <a href="{{ route('face-enrollment.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Employee Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $employee->user->name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 text-sm text-gray-600">
                        <span><i class="fas fa-id-badge mr-1"></i> ID: {{ $employee->employee_id }}</span>
                        <span><i class="fas fa-briefcase mr-1"></i> {{ $employee->position }}</span>
                        <span><i class="fas fa-building mr-1"></i> {{ $employee->department ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="text-right">
                    @if($employee->user->hasFaceEnrolled())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Already Enrolled
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Not Enrolled
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Face Enrollment Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200"
             x-data="faceEnrollmentForm()">

            <!-- Instructions -->
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Petunjuk Face Enrollment
                </h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Pastikan wajah karyawan terlihat jelas dan menghadap kamera</li>
                    <li>• Hindari bayangan atau pencahayaan yang terlalu gelap/terang</li>
                    <li>• Jangan menggunakan masker atau kacamata hitam</li>
                    <li>• Posisikan wajah di tengah frame kamera</li>
                    <li>• Klik "Ambil Foto" saat wajah sudah dalam posisi yang baik</li>
                </ul>
            </div>

            <!-- Camera Section -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Camera Preview -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-video mr-2 text-blue-600"></i>
                            Live Camera
                        </h4>

                        <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                            <video x-ref="video"
                                   autoplay
                                   muted
                                   playsinline
                                   class="w-full h-full object-cover">
                            </video>

                            <!-- Face detection overlay -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <div class="w-48 h-56 border-2 border-white rounded-lg opacity-70 shadow-lg"></div>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <div class="bg-black bg-opacity-50 text-white text-sm p-2 rounded">
                                        Posisikan wajah di dalam frame
                                    </div>
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
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-video mr-2"></i>
                                Aktifkan Kamera
                            </button>

                            <button @click="capturePhoto()"
                                    x-show="cameraReady && !photoTaken"
                                    :disabled="!canCapture"
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
                        <h4 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-image mr-2 text-green-600"></i>
                            Hasil Foto
                        </h4>

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

                        <!-- Photo Quality Check -->
                        <div x-show="photoTaken" class="space-y-2">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium">Foto berhasil diambil</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Form -->
            <div class="p-6 border-t border-gray-200" x-show="photoTaken">
                <form @submit.prevent="submitEnrollment()">
                    <!-- Confirmation -->
                    <div class="mb-6">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox"
                                   x-model="confirmEnrollment"
                                   id="confirm_enrollment"
                                   required
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <label for="confirm_enrollment" class="block text-sm font-medium text-gray-700">
                                    Konfirmasi Enrollment
                                </label>
                                <p class="text-sm text-gray-600 mt-1">
                                    Saya konfirmasi bahwa foto wajah di atas adalah milik karyawan
                                    <strong>{{ $employee->user->name }}</strong> dan akan digunakan
                                    untuk sistem presensi face recognition.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                :disabled="!canSubmit"
                                class="inline-flex items-center px-8 py-4 bg-purple-600 text-white text-lg font-medium rounded-lg hover:bg-purple-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-save mr-3"></i>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan Enrollment'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">
                <i class="fas fa-lightbulb mr-2"></i>
                Tips untuk Enrollment yang Berhasil
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-yellow-800">
                <div>
                    <h4 class="font-medium mb-1">Pencahayaan</h4>
                    <p>Pastikan pencahayaan cukup dan merata. Hindari backlight atau bayangan.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Posisi Wajah</h4>
                    <p>Wajah harus menghadap langsung ke kamera, tidak miring atau menoleh.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Ekspresi</h4>
                    <p>Gunakan ekspresi netral tanpa senyum berlebihan atau memicingkan mata.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-1">Aksesoris</h4>
                    <p>Lepas kacamata, masker, atau aksesoris yang menutupi wajah.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function faceEnrollmentForm() {
                return {
                    // Camera properties
                    cameraReady: false,
                    photoTaken: false,
                    photoData: null,
                    stream: null,

                    // Form properties
                    confirmEnrollment: false,
                    submitting: false,

                    // Computed properties
                    get canCapture() {
                        return this.cameraReady && !this.submitting;
                    },

                    get canSubmit() {
                        return this.photoTaken && this.confirmEnrollment && !this.submitting;
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
                        this.confirmEnrollment = false;
                        this.startCamera();
                    },

                    // Form submission
                    async submitEnrollment() {
                        if (!this.canSubmit) return;

                        this.submitting = true;
                        showLoading();

                        try {
                            const formData = {
                                photo: this.photoData,
                                confirm_enrollment: this.confirmEnrollment
                            };

                            const response = await fetch('{{ route("face-enrollment.enroll", $employee) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(formData)
                            });

                            const result = await response.json();

                            if (result.success) {
                                alert('Face enrollment berhasil! Karyawan dapat melakukan presensi.');
                                window.location.href = '{{ route("face-enrollment.index") }}';
                            } else {
                                alert('Enrollment gagal: ' + result.message);
                                this.retakePhoto();
                            }

                        } catch (error) {
                            console.error('Enrollment error:', error);
                            alert('Terjadi kesalahan sistem. Silakan coba lagi.');
                            this.retakePhoto();
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
