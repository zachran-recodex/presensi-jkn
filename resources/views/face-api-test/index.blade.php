<x-app-layout>
    <div x-data="faceApiTester()" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Face API Testing Dashboard</h1>
                    <p class="text-gray-600 mt-2">Test Biznet Face Recognition API integration</p>
                </div>
            </div>
        </div>

        <!-- API Status -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">API Connection Status</h2>
            <div class="flex space-x-4">
                <button @click="testConnection"
                        :disabled="loading"
                        class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-wifi mr-2"></i>Test Connection
                </button>
                <button @click="getCounters"
                        :disabled="loading"
                        class="bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-chart-bar mr-2"></i>Get Counters
                </button>
                <button @click="getMyFaceGalleries"
                        :disabled="loading"
                        class="bg-purple-500 hover:bg-purple-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-list mr-2"></i>My Galleries
                </button>
            </div>
        </div>

        <!-- Results Display -->
        <div x-show="result" class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Last Result</h3>
                <button @click="clearResult" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 overflow-auto max-h-96">
                <pre x-text="result" class="text-sm"></pre>
            </div>
        </div>

        <!-- Testing Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <template x-for="(tab, index) in tabs" :key="index">
                        <button @click="activeTab = index"
                                :class="activeTab === index ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition duration-200"
                                x-text="tab.name">
                        </button>
                    </template>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- FaceGallery Management -->
                <div x-show="activeTab === 0" class="space-y-6">
                    <h3 class="text-lg font-semibold">FaceGallery Management</h3>

                    <!-- Create FaceGallery -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium mb-3">Create FaceGallery</h4>
                        <div class="flex space-x-4">
                            <input x-model="createGalleryId"
                                   type="text"
                                   placeholder="FaceGallery ID (e.g., test-gallery-2024)"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button @click="createFaceGallery"
                                    :disabled="!createGalleryId || loading"
                                    class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                Create
                            </button>
                        </div>
                    </div>

                    <!-- Delete FaceGallery -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium mb-3">Delete FaceGallery</h4>
                        <div class="flex space-x-4">
                            <input x-model="deleteGalleryId"
                                   type="text"
                                   placeholder="FaceGallery ID to delete"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button @click="deleteFaceGallery"
                                    :disabled="!deleteGalleryId || loading"
                                    class="bg-red-500 hover:bg-red-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Face Enrollment -->
                <div x-show="activeTab === 1" class="space-y-6">
                    <h3 class="text-lg font-semibold">Face Enrollment Testing</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Camera/Photo -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Capture Photo</h4>
                            <div class="space-y-4">
                                <video x-ref="enrollVideo"
                                       class="w-full h-48 bg-black rounded-lg"
                                       autoplay
                                       muted
                                       x-show="!enrollPhoto">
                                </video>
                                <img x-show="enrollPhoto"
                                     :src="enrollPhoto"
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="flex space-x-2">
                                    <button @click="startEnrollCamera"
                                            x-show="!cameraActive"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera mr-1"></i>Start Camera
                                    </button>
                                    <button @click="captureEnrollPhoto"
                                            x-show="cameraActive && !enrollPhoto"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera-retro mr-1"></i>Capture
                                    </button>
                                    <button @click="retakeEnrollPhoto"
                                            x-show="enrollPhoto"
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-redo mr-1"></i>Retake
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Enrollment Form -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Enrollment Details</h4>
                            <div class="space-y-3">
                                <input x-model="enrollUserId"
                                       type="text"
                                       placeholder="User ID (e.g., test-user-001)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input x-model="enrollUserName"
                                       type="text"
                                       placeholder="User Name (e.g., John Doe)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input x-model="enrollGalleryId"
                                       type="text"
                                       placeholder="FaceGallery ID (optional)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button @click="testEnrollFace"
                                        :disabled="!enrollPhoto || !enrollUserId || !enrollUserName || loading"
                                        class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-user-plus mr-2"></i>Enroll Face
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Face Verification -->
                <div x-show="activeTab === 2" class="space-y-6">
                    <h3 class="text-lg font-semibold">Face Verification Testing</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Camera/Photo -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Verification Photo</h4>
                            <div class="space-y-4">
                                <video x-ref="verifyVideo"
                                       class="w-full h-48 bg-black rounded-lg"
                                       autoplay
                                       muted
                                       x-show="!verifyPhoto">
                                </video>
                                <img x-show="verifyPhoto"
                                     :src="verifyPhoto"
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="flex space-x-2">
                                    <button @click="startVerifyCamera"
                                            x-show="!cameraActive"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera mr-1"></i>Start Camera
                                    </button>
                                    <button @click="captureVerifyPhoto"
                                            x-show="cameraActive && !verifyPhoto"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera-retro mr-1"></i>Capture
                                    </button>
                                    <button @click="retakeVerifyPhoto"
                                            x-show="verifyPhoto"
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-redo mr-1"></i>Retake
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Form -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Verification Details</h4>
                            <div class="space-y-3">
                                <input x-model="verifyUserId"
                                       type="text"
                                       placeholder="User ID to verify against"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input x-model="verifyGalleryId"
                                       type="text"
                                       placeholder="FaceGallery ID (optional)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button @click="testVerifyFace"
                                        :disabled="!verifyPhoto || !verifyUserId || loading"
                                        class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-check-circle mr-2"></i>Verify Face
                                </button>
                                <button @click="testIdentifyFace"
                                        :disabled="!verifyPhoto || loading"
                                        class="w-full bg-purple-500 hover:bg-purple-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-search mr-2"></i>Identify Face (1:N)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compare Images -->
                <div x-show="activeTab === 3" class="space-y-6">
                    <h3 class="text-lg font-semibold">Compare Images Testing</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Source Image -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Source Image</h4>
                            <div class="space-y-4">
                                <video x-ref="sourceVideo"
                                       class="w-full h-48 bg-black rounded-lg"
                                       autoplay
                                       muted
                                       x-show="!sourceImage">
                                </video>
                                <img x-show="sourceImage"
                                     :src="sourceImage"
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="flex space-x-2">
                                    <button @click="startSourceCamera"
                                            x-show="!cameraActive"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera mr-1"></i>Start Camera
                                    </button>
                                    <button @click="captureSourceImage"
                                            x-show="cameraActive && !sourceImage"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera-retro mr-1"></i>Capture
                                    </button>
                                    <button @click="retakeSourceImage"
                                            x-show="sourceImage"
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-redo mr-1"></i>Retake
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Target Image -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Target Image</h4>
                            <div class="space-y-4">
                                <video x-ref="targetVideo"
                                       class="w-full h-48 bg-black rounded-lg"
                                       autoplay
                                       muted
                                       x-show="!targetImage">
                                </video>
                                <img x-show="targetImage"
                                     :src="targetImage"
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="flex space-x-2">
                                    <button @click="startTargetCamera"
                                            x-show="!cameraActive"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera mr-1"></i>Start Camera
                                    </button>
                                    <button @click="captureTargetImage"
                                            x-show="cameraActive && !targetImage"
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-camera-retro mr-1"></i>Capture
                                    </button>
                                    <button @click="retakeTargetImage"
                                            x-show="targetImage"
                                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded text-sm">
                                        <i class="fas fa-redo mr-1"></i>Retake
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button @click="testCompareImages"
                                :disabled="!sourceImage || !targetImage || loading"
                                class="bg-indigo-500 hover:bg-indigo-600 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg transition duration-200">
                            <i class="fas fa-balance-scale mr-2"></i>Compare Images
                        </button>
                    </div>
                </div>

                <!-- Face Management -->
                <div x-show="activeTab === 4" class="space-y-6">
                    <h3 class="text-lg font-semibold">Face Management</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- List Faces -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">List Enrolled Faces</h4>
                            <div class="space-y-3">
                                <input x-model="listGalleryId"
                                       type="text"
                                       placeholder="FaceGallery ID (optional)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button @click="listFaces"
                                        :disabled="loading"
                                        class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-list mr-2"></i>List Faces
                                </button>
                            </div>
                        </div>

                        <!-- Delete Face -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium mb-3">Delete Enrolled Face</h4>
                            <div class="space-y-3">
                                <input x-model="deleteUserId"
                                       type="text"
                                       placeholder="User ID to delete"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input x-model="deleteUserGalleryId"
                                       type="text"
                                       placeholder="FaceGallery ID (optional)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button @click="deleteFace"
                                        :disabled="!deleteUserId || loading"
                                        class="w-full bg-red-500 hover:bg-red-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-trash mr-2"></i>Delete Face
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div x-show="loading"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                <span class="text-gray-700">Processing...</span>
            </div>
        </div>
    </div>

    <script>
        function faceApiTester() {
            return {
                loading: false,
                result: '',
                activeTab: 0,
                cameraActive: false,

                // Tabs
                tabs: [
                    { name: 'Gallery Management' },
                    { name: 'Face Enrollment' },
                    { name: 'Face Verification' },
                    { name: 'Compare Images' },
                    { name: 'Face Management' }
                ],

                // Gallery Management
                createGalleryId: '',
                deleteGalleryId: '',

                // Face Enrollment
                enrollPhoto: '',
                enrollUserId: '',
                enrollUserName: '',
                enrollGalleryId: '',

                // Face Verification
                verifyPhoto: '',
                verifyUserId: '',
                verifyGalleryId: '',

                // Compare Images
                sourceImage: '',
                targetImage: '',

                // Face Management
                listGalleryId: '',
                deleteUserId: '',
                deleteUserGalleryId: '',

                init() {
                    // Setup CSRF token for all AJAX requests
                    fetch.defaults = {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    };
                },

                async makeRequest(url, data = {}) {
                    this.loading = true;
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();
                        this.displayResult(result);
                        return result;
                    } catch (error) {
                        this.displayResult({ success: false, message: 'Network error: ' + error.message });
                    } finally {
                        this.loading = false;
                    }
                },

                displayResult(result) {
                    this.result = JSON.stringify(result, null, 2);

                    // Show notification
                    const message = result.success ? 'Success!' : 'Error: ' + result.message;
                    const bgColor = result.success ? 'bg-green-500' : 'bg-red-500';

                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                },

                clearResult() {
                    this.result = '';
                },

                // API Methods
                async testConnection() {
                    await this.makeRequest('/face-api-test/connection');
                },

                async getCounters() {
                    await this.makeRequest('/face-api-test/counters');
                },

                async getMyFaceGalleries() {
                    await this.makeRequest('/face-api-test/galleries');
                },

                async createFaceGallery() {
                    await this.makeRequest('/face-api-test/gallery/create', {
                        facegallery_id: this.createGalleryId
                    });
                },

                async deleteFaceGallery() {
                    await this.makeRequest('/face-api-test/gallery/delete', {
                        facegallery_id: this.deleteGalleryId
                    });
                },

                async testEnrollFace() {
                    await this.makeRequest('/face-api-test/enroll', {
                        user_id: this.enrollUserId,
                        user_name: this.enrollUserName,
                        photo: this.enrollPhoto,
                        facegallery_id: this.enrollGalleryId
                    });
                },

                async testVerifyFace() {
                    await this.makeRequest('/face-api-test/verify', {
                        user_id: this.verifyUserId,
                        photo: this.verifyPhoto,
                        facegallery_id: this.verifyGalleryId
                    });
                },

                async testIdentifyFace() {
                    await this.makeRequest('/face-api-test/identify', {
                        photo: this.verifyPhoto,
                        facegallery_id: this.verifyGalleryId
                    });
                },

                async testCompareImages() {
                    await this.makeRequest('/face-api-test/compare', {
                        source_image: this.sourceImage,
                        target_image: this.targetImage
                    });
                },

                async listFaces() {
                    await this.makeRequest('/face-api-test/faces/list', {
                        facegallery_id: this.listGalleryId
                    });
                },

                async deleteFace() {
                    await this.makeRequest('/face-api-test/faces/delete', {
                        user_id: this.deleteUserId,
                        facegallery_id: this.deleteUserGalleryId
                    });
                },

                // Camera Methods
                async startCamera(videoRef) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 640, height: 480 }
                        });
                        videoRef.srcObject = stream;
                        this.cameraActive = true;
                    } catch (error) {
                        alert('Camera access denied: ' + error.message);
                    }
                },

                stopCamera() {
                    const videos = document.querySelectorAll('video');
                    videos.forEach(video => {
                        if (video.srcObject) {
                            video.srcObject.getTracks().forEach(track => track.stop());
                            video.srcObject = null;
                        }
                    });
                    this.cameraActive = false;
                },

                capturePhoto(videoRef) {
                    const canvas = document.createElement('canvas');
                    canvas.width = videoRef.videoWidth;
                    canvas.height = videoRef.videoHeight;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(videoRef, 0, 0);

                    return canvas.toDataURL('image/jpeg', 0.8);
                },

                // Enrollment Camera Methods
                async startEnrollCamera() {
                    await this.startCamera(this.$refs.enrollVideo);
                },

                captureEnrollPhoto() {
                    this.enrollPhoto = this.capturePhoto(this.$refs.enrollVideo);
                    this.stopCamera();
                },

                retakeEnrollPhoto() {
                    this.enrollPhoto = '';
                },

                // Verify Camera Methods
                async startVerifyCamera() {
                    await this.startCamera(this.$refs.verifyVideo);
                },

                captureVerifyPhoto() {
                    this.verifyPhoto = this.capturePhoto(this.$refs.verifyVideo);
                    this.stopCamera();
                },

                retakeVerifyPhoto() {
                    this.verifyPhoto = '';
                },

                // Source Camera Methods
                async startSourceCamera() {
                    await this.startCamera(this.$refs.sourceVideo);
                },

                captureSourceImage() {
                    this.sourceImage = this.capturePhoto(this.$refs.sourceVideo);
                    this.stopCamera();
                },

                retakeSourceImage() {
                    this.sourceImage = '';
                },

                // Target Camera Methods
                async startTargetCamera() {
                    await this.startCamera(this.$refs.targetVideo);
                },

                captureTargetImage() {
                    this.targetImage = this.capturePhoto(this.$refs.targetVideo);
                    this.stopCamera();
                },

                retakeTargetImage() {
                    this.targetImage = '';
                }
            }
        }
    </script>
</x-app-layout>
