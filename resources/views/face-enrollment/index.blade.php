<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-face-smile mr-2 text-purple-600"></i>
                    Face Enrollment
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola enrollment wajah karyawan untuk sistem presensi</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshStats()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Stats
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $employees->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-face-smile"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sudah Enrolled</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $employees->where('user.face_id', '!=', null)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 text-red-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-face-frown"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Belum Enrolled</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $employees->where('user.face_id', null)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-lg mr-4">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Enrollment Rate</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $employees->total() > 0 ? round(($employees->where('user.face_id', '!=', null)->count() / $employees->total()) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-cloud mr-2 text-blue-600"></i>
                Status API Biznet Face
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(isset($apiCounters['error']))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="text-red-800 font-medium">API Error</span>
                        </div>
                        <p class="text-sm text-red-600 mt-1">{{ $apiCounters['error'] }}</p>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-green-800 font-medium">API Status</span>
                        </div>
                        <p class="text-sm text-green-600 mt-1">Connected</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                            <span class="text-blue-800 font-medium">API Limits</span>
                        </div>
                        <p class="text-sm text-blue-600 mt-1">
                            {{ json_encode($apiCounters) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Daftar Karyawan
                </h3>
            </div>

            @if($employees->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($employees as $employee)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <!-- Employee Info -->
                                <div class="flex items-center space-x-4">
                                    <div class="bg-{{ $employee->user->hasFaceEnrolled() ? 'green' : 'gray' }}-100 text-{{ $employee->user->hasFaceEnrolled() ? 'green' : 'gray' }}-600 p-3 rounded-full">
                                        <i class="fas fa-{{ $employee->user->hasFaceEnrolled() ? 'face-smile' : 'user' }}"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $employee->user->name }}
                                        </h4>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>
                                                <i class="fas fa-id-badge mr-1"></i>
                                                {{ $employee->employee_id }}
                                            </span>
                                            <span>
                                                <i class="fas fa-briefcase mr-1"></i>
                                                {{ $employee->position }}
                                            </span>
                                            <span>
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $employee->department ?: 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Actions -->
                                <div class="flex items-center space-x-4">
                                    <!-- Enrollment Status -->
                                    @if($employee->user->hasFaceEnrolled())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Enrolled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Not Enrolled
                                        </span>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2">
                                        @if(!$employee->user->hasFaceEnrolled())
                                            <a href="{{ route('face-enrollment.show', $employee) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-camera mr-2"></i>
                                                Enroll
                                            </a>
                                        @else
                                            <button onclick="testVerification({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-eye mr-2"></i>
                                                Test
                                            </button>

                                            <button onclick="reenrollFace({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                                <i class="fas fa-redo mr-2"></i>
                                                Re-enroll
                                            </button>

                                            <button onclick="deleteFace({{ $employee->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-trash mr-2"></i>
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $employees->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-users text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Karyawan</h3>
                    <p class="text-gray-600 mb-6">Belum ada karyawan yang terdaftar dalam sistem.</p>
                    <a href="{{ route('employees.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Karyawan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Test Verification Modal -->
    <div x-data="{ showModal: false, employee: null }" x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Face Verification</h3>
                <div class="mb-4">
                    <video id="testVideo" autoplay muted playsinline class="w-full rounded-lg bg-gray-100" style="aspect-ratio: 4/3;"></video>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button onclick="captureTestPhoto()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Test Verification
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentEmployeeId = null;
            let testStream = null;

            // Test Verification
            async function testVerification(employeeId) {
                currentEmployeeId = employeeId;
                Alpine.store('modal', { showModal: true });

                try {
                    testStream = await navigator.mediaDevices.getUserMedia({ video: true });
                    document.getElementById('testVideo').srcObject = testStream;
                } catch (error) {
                    alert('Cannot access camera: ' + error.message);
                }
            }

            async function captureTestPhoto() {
                if (!testStream) return;

                const video = document.getElementById('testVideo');
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);

                const photoData = canvas.toDataURL('image/jpeg', 0.8);

                // Stop camera
                testStream.getTracks().forEach(track => track.stop());
                testStream = null;

                // Close modal
                Alpine.store('modal', { showModal: false });

                // Send to server
                showLoading();
                try {
                    const response = await fetch(`/face-enrollment/${currentEmployeeId}/test-verification`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ photo: photoData })
                    });

                    const result = await response.json();
                    alert(result.message);
                } catch (error) {
                    alert('Test verification failed: ' + error.message);
                } finally {
                    hideLoading();
                }
            }

            // Re-enroll Face
            async function reenrollFace(employeeId) {
                if (!confirm('Are you sure you want to re-enroll this employee\'s face?')) return;

                showLoading();
                try {
                    const response = await fetch(`/face-enrollment/${employeeId}/reenroll`, {
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
                        alert('Re-enrollment failed: ' + result.message);
                    }
                } catch (error) {
                    alert('Re-enrollment error: ' + error.message);
                } finally {
                    hideLoading();
                }
            }

            // Delete Face
            async function deleteFace(employeeId) {
                if (!confirm('Are you sure you want to delete this employee\'s face data?')) return;

                showLoading();
                try {
                    const response = await fetch(`/face-enrollment/${employeeId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        location.reload();
                    } else {
                        alert('Delete failed: ' + result.message);
                    }
                } catch (error) {
                    alert('Delete error: ' + error.message);
                } finally {
                    hideLoading();
                }
            }

            // Refresh Stats
            async function refreshStats() {
                showLoading();
                try {
                    const response = await fetch('/face-enrollment/stats');
                    const result = await response.json();

                    // Update stats display
                    console.log('Stats updated:', result);
                    location.reload();
                } catch (error) {
                    console.error('Stats refresh error:', error);
                } finally {
                    hideLoading();
                }
            }
        </script>
    @endpush
</x-app-layout>
