<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Face Enrollment - ') }}{{ $employee->user->name }}
            </h2>
            <a href="{{ route('face-enrollment.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Employee Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Karyawan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-user mr-1"></i>Nama Lengkap:
                                </span>
                                <p class="text-gray-900">{{ $employee->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-id-card mr-1"></i>ID Karyawan:
                                </span>
                                <p class="text-gray-900">{{ $employee->employee_id }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-envelope mr-1"></i>Email:
                                </span>
                                <p class="text-gray-900">{{ $employee->user->email }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-briefcase mr-1"></i>Jabatan:
                                </span>
                                <p class="text-gray-900">{{ $employee->position }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-building mr-1"></i>Department:
                                </span>
                                <p class="text-gray-900">{{ $employee->department ?: 'No Department' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Lokasi Kerja:
                                </span>
                                <p class="text-gray-900">{{ $employee->location->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-clipboard-check mr-2"></i>Status Enrollment
                    </h3>

                    @if($employee->user->hasFaceEnrolled())
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-400 mr-3 text-xl"></i>
                                <div class="flex-1">
                                    <p class="text-green-800 font-medium">Wajah Sudah Terdaftar</p>
                                    <p class="text-green-700 text-sm">Face ID: {{ $employee->user->face_id }}</p>
                                    <p class="text-green-600 text-xs mt-1">Karyawan sudah dapat melakukan presensi menggunakan face recognition.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 text-xl"></i>
                                <div>
                                    <p class="text-yellow-800 font-medium">Wajah Belum Terdaftar</p>
                                    <p class="text-yellow-700 text-sm">Karyawan belum dapat melakukan presensi. Lakukan enrollment terlebih dahulu.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrollment/Test Form -->
            <div x-data="faceEnrollmentForm()" x-init="init()" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        @if($employee->user->hasFaceEnrolled())
                            <i class="fas fa-user-check mr-2"></i>Test Verifikasi Wajah
                        @else
                            <i class="fas fa-user-plus mr-2"></i>Enrollment Wajah
                        @endif
                    </h3>

                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5 text-xl"></i>
                            <div>
                                <p class="text-blue-800 font-medium">Instruksi:</p>
                                <ul class="text-blue-700 text-sm mt-1 space-y-1 list-disc list-inside">
                                    <li>Pastikan pencahayaan cukup dan wajah terlihat jelas</li>
                                    <li>Posisikan wajah menghadap langsung ke kamera</li>
                                    <li>Lepaskan masker, kacamata, atau aksesori yang menutupi wajah</li>
                                    <li>Jaga jarak sekitar 30-50cm dari kamera</li>
                                    <li>Pastikan hanya ada satu wajah dalam frame</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Section -->
                    <div class="space-y-6">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-lg mb-4">
                                <i class="fas fa-camera mr-2"></i>Capture Foto Wajah
                            </h4>

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
                                        <i class="fas fa-spinner fa-spin text-blue-600 text-3xl mb-2"></i>
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
                                            <i class="fas fa-sync-alt mr-1"></i>Coba Lagi
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
                                    <i class="fas fa-camera mr-1"></i>
                                    <span>Ambil Foto</span>
                                </button>

                                <button
                                    @click="retakePhoto()"
                                    x-show="photoTaken"
                                    class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2"
                                >
                                    <i class="fas fa-redo mr-1"></i>
                                    <span>Ambil Ulang</span>
                                </button>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="border-t pt-6">
                            @if($employee->user->hasFaceEnrolled())
                                <!-- Test Verification -->
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-lg">
                                        <i class="fas fa-vial mr-2"></i>Test Verifikasi
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Gunakan foto yang diambil untuk menguji apakah sistem dapat mengenali wajah yang sudah terdaftar.
                                    </p>

                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button
                                            @click="testVerification()"
                                            :disabled="!photoTaken || processing"
                                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                        >
                                            <i x-show="!processing" class="fas fa-check-circle mr-1"></i>
                                            <i x-show="processing" class="fas fa-spinner fa-spin mr-1"></i>
                                            <span x-text="processing ? 'Testing...' : 'Test Verifikasi'"></span>
                                        </button>

                                        <button
                                            @click="showReenrollConfirm = true"
                                            class="flex-1 bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 flex items-center justify-center space-x-2"
                                        >
                                            <i class="fas fa-sync-alt mr-1"></i>
                                            <span>Re-enrollment</span>
                                        </button>

                                        <button
                                            @click="showDeleteConfirm = true"
                                            class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 flex items-center justify-center space-x-2"
                                        >
                                            <i class="fas fa-trash-alt mr-1"></i>
                                            <span>Hapus Enrollment</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- New Enrollment -->
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-lg">
                                        <i class="fas fa-user-plus mr-2"></i>Enrollment Wajah Baru
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Pastikan foto wajah sudah diambil dengan baik, lalu centang konfirmasi untuk memulai proses enrollment.
                                    </p>

                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="checkbox"
                                            id="confirm_enrollment"
                                            x-model="confirmEnrollment"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        >
                                        <label for="confirm_enrollment" class="text-sm text-gray-700">
                                            Saya konfirmasi bahwa foto sudah benar dan siap untuk di-enroll
                                        </label>
                                    </div>

                                    <button
                                        @click="submitEnrollment()"
                                        :disabled="!photoTaken || !confirmEnrollment || processing"
                                        class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                    >
                                        <i x-show="!processing" class="fas fa-plus-circle mr-1"></i>
                                        <i x-show="processing" class="fas fa-spinner fa-spin mr-1"></i>
                                        <span x-text="processing ? 'Processing Enrollment...' : 'Mulai Enrollment'"></span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900 mb-3">
                                <i class="fas fa-clipboard-list mr-2"></i>Checklist Requirements
                            </h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center space-x-2">
                                    <div :class="cameraReady ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="cameraReady ? 'text-green-700' : 'text-gray-600'">Kamera siap</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div :class="photoTaken ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="photoTaken ? 'text-green-700' : 'text-gray-600'">Foto wajah diambil</span>
                                </div>
                                @if(!$employee->user->hasFaceEnrolled())
                                <div class="flex items-center space-x-2">
                                    <div :class="confirmEnrollment ? 'bg-green-500' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
                                    <span :class="confirmEnrollment ? 'text-green-700' : 'text-gray-600'">Konfirmasi enrollment</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Re-enrollment Confirmation Modal -->
    <div x-data="{ showReenrollConfirm: false }"
         x-show="showReenrollConfirm"
         x-cloak
         @show-reenroll-confirm.window="showReenrollConfirm = true"
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
                        <div class="bg-yellow-100 mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Re-enrollment</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin melakukan re-enrollment?
                                    Data wajah lama akan dihapus dan diganti dengan yang baru.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="submitReenrollment(); showReenrollConfirm = false"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>Ya, Re-enroll
                    </button>
                    <button @click="showReenrollConfirm = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDeleteConfirm: false }"
         x-show="showDeleteConfirm"
         x-cloak
         @show-delete-confirm.window="showDeleteConfirm = true"
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
                        <div class="bg-red-100 mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Hapus Enrollment</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus enrollment wajah?
                                    Karyawan tidak akan bisa melakukan presensi sampai di-enroll kembali.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="submitDeletion(); showDeleteConfirm = false"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-trash-alt mr-2"></i>Ya, Hapus
                    </button>
                    <button @click="showDeleteConfirm = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div x-data="{ show: false, title: '', message: '', type: 'success', details: null }"
         x-show="show"
         x-cloak
         @enrollment-result.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type; details = $event.detail.details"
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
                            <i x-show="type === 'success'" class="fas fa-check text-green-600 text-xl"></i>
                            <i x-show="type === 'error'" class="fas fa-times text-red-600 text-xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                                <div x-show="details" class="mt-3 text-xs text-gray-600">
                                    <template x-if="details">
                                        <div>
                                            <p x-show="details.verified !== undefined"><strong>Verified:</strong> <span x-text="details.verified ? 'Yes' : 'No'"></span></p>
                                            <p x-show="details.similarity !== undefined"><strong>Similarity:</strong> <span x-text="details.similarity + '%'"></span></p>
                                            <p x-show="details.threshold !== undefined"><strong>Threshold:</strong> <span x-text="details.threshold + '%'"></span></p>
                                            <p x-show="details.masker !== undefined"><strong>Mask Detected:</strong> <span x-text="details.masker ? 'Yes' : 'No'"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="show = false; if(type === 'success' && (title.includes('Enrollment') || title.includes('Re-enrollment') || title.includes('Hapus'))) location.reload()"
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
        function faceEnrollmentForm() {
            return {
                // Camera
                cameraReady: false,
                cameraError: false,
                cameraErrorMessage: '',
                photoTaken: false,
                photoData: null,
                stream: null,

                // Form
                confirmEnrollment: false,
                processing: false,

                // Modal states
                showReenrollConfirm: false,
                showDeleteConfirm: false,

                async init() {
                    await this.initCamera();
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

                async submitEnrollment() {
                    if (!this.photoData || !this.confirmEnrollment) return;

                    this.processing = true;

                    try {
                        const response = await fetch(`/face-enrollment/{{ $employee->id }}/enroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                photo: this.photoData,
                                confirm_enrollment: 1
                            })
                        });

                        const result = await response.json();

                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: result.success ? 'success' : 'error',
                                title: result.success ? 'Enrollment Berhasil!' : 'Enrollment Gagal!',
                                message: result.message,
                                details: result.data
                            }
                        }));
                    } catch (error) {
                        console.error('Enrollment error:', error);
                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: 'error',
                                title: 'Error!',
                                message: 'Terjadi kesalahan jaringan. Silakan coba lagi.'
                            }
                        }));
                    } finally {
                        this.processing = false;
                    }
                },

                async testVerification() {
                    if (!this.photoData) return;

                    this.processing = true;

                    try {
                        const response = await fetch(`/face-enrollment/{{ $employee->id }}/test`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                photo: this.photoData
                            })
                        });

                        const result = await response.json();

                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: result.success ? 'success' : 'error',
                                title: result.success ? 'Test Verifikasi' : 'Test Gagal',
                                message: result.message,
                                details: result.data
                            }
                        }));
                    } catch (error) {
                        console.error('Test error:', error);
                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: 'error',
                                title: 'Error!',
                                message: 'Terjadi kesalahan jaringan.'
                            }
                        }));
                    } finally {
                        this.processing = false;
                    }
                },

                async submitReenrollment() {
                    if (!this.photoData) return;

                    this.processing = true;

                    try {
                        const response = await fetch(`/face-enrollment/{{ $employee->id }}/reenroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                photo: this.photoData,
                                confirm_enrollment: 1
                            })
                        });

                        const result = await response.json();

                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: result.success ? 'success' : 'error',
                                title: result.success ? 'Re-enrollment Berhasil!' : 'Re-enrollment Gagal!',
                                message: result.message,
                                details: result.data
                            }
                        }));
                    } catch (error) {
                        console.error('Re-enrollment error:', error);
                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: 'error',
                                title: 'Error!',
                                message: 'Terjadi kesalahan jaringan.'
                            }
                        }));
                    } finally {
                        this.processing = false;
                    }
                },

                async submitDeletion() {
                    this.processing = true;

                    try {
                        const response = await fetch(`/face-enrollment/{{ $employee->id }}/delete`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: result.success ? 'success' : 'error',
                                title: result.success ? 'Enrollment Dihapus!' : 'Gagal Hapus!',
                                message: result.message,
                                details: result.data
                            }
                        }));
                    } catch (error) {
                        console.error('Delete error:', error);
                        window.dispatchEvent(new CustomEvent('enrollment-result', {
                            detail: {
                                type: 'error',
                                title: 'Error!',
                                message: 'Terjadi kesalahan jaringan.'
                            }
                        }));
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
