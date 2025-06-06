<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Services\FaceRecognitionService;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class FaceEnrollmentController extends Controller
{
    protected $faceService;
    protected $imageService;

    public function __construct(FaceRecognitionService $faceService, ImageProcessingService $imageService)
    {
        $this->middleware(['auth', 'admin']);
        $this->faceService = $faceService;
        $this->imageService = $imageService;
    }

    /**
     * Show face enrollment dashboard
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'location'])
            ->active()
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhere('employee_id', 'like', '%' . $search . '%');
        }

        // Filter by enrollment status
        if ($request->has('enrollment_status')) {
            if ($request->enrollment_status === 'enrolled') {
                $query->whereHas('user', function($q) {
                    $q->whereNotNull('face_id');
                });
            } elseif ($request->enrollment_status === 'not_enrolled') {
                $query->whereHas('user', function($q) {
                    $q->whereNull('face_id');
                });
            }
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        $employees = $query->paginate(20);

        // Get API counters with error handling
        try {
            $apiCounters = $this->faceService->getCounters();
        } catch (Exception $e) {
            Log::warning('Failed to get API counters: ' . $e->getMessage());
            $apiCounters = [
                'status' => 'error',
                'message' => 'API tidak tersedia',
                'error' => $e->getMessage()
            ];
        }

        // Get enrollment statistics
        $stats = $this->getEnrollmentStats();

        // Get departments for filter
        $departments = Employee::distinct()->pluck('department')->filter()->sort();

        return view('face-enrollment.index', compact(
            'employees',
            'apiCounters',
            'stats',
            'departments'
        ));
    }

    /**
     * Show enrollment form for specific employee
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'location']);

        // Check if employee is active
        if ($employee->status !== 'active') {
            return redirect()->route('face-enrollment.index')
                ->with('error', 'Karyawan tidak aktif. Tidak dapat melakukan enrollment.');
        }

        // Get enrollment history if exists
        $enrollmentHistory = null;
        if ($employee->user->hasFaceEnrolled()) {
            // You can store enrollment history in a separate table if needed
            // For now, we'll just show current status
        }

        return view('face-enrollment.form', compact('employee', 'enrollmentHistory'));
    }

    /**
     * Process face enrollment
     */
    public function enroll(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|string',
            'confirm_enrollment' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Check if employee is active
            if ($employee->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak aktif. Tidak dapat melakukan enrollment.'
                ], 422);
            }

            // Check if already enrolled
            if ($employee->user->hasFaceEnrolled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan sudah terdaftar. Gunakan re-enrollment jika ingin mendaftar ulang.'
                ], 422);
            }

            // Validate and process image
            $imageValidation = $this->imageService->validateBase64Image($request->photo);
            if (!$imageValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak valid: ' . $imageValidation['error']
                ], 422);
            }

            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Generate unique face ID
            $faceId = 'emp_' . $employee->employee_id . '_' . time();

            // Call Biznet Face API
            $result = $this->faceService->enrollFace(
                $faceId,
                $employee->user->name,
                $base64Photo
            );

            // Check for success (handle both direct status and risetai wrapper)
            $isSuccess = false;
            $statusMessage = 'Enrollment gagal: Kesalahan tidak diketahui';

            if (isset($result['status']) && $result['status'] === 'success') {
                $isSuccess = true;
                $statusMessage = $result['status_message'] ?? 'Enrollment berhasil';
            } elseif (isset($result['risetai'])) {
                // Handle risetai wrapper response
                $risetaiData = $result['risetai'];
                if (isset($risetaiData['status']) && ($risetaiData['status'] === '200' || $risetaiData['status'] === 'success')) {
                    $isSuccess = true;
                    $statusMessage = $risetaiData['status_message'] ?? 'Enrollment berhasil';
                } else {
                    $statusMessage = $risetaiData['status_message'] ?? 'Enrollment gagal';
                }
            }

            if ($isSuccess) {
                // Update user with face_id
                $employee->user->update(['face_id' => $faceId]);

                // Log enrollment activity
                Log::info('Face enrollment successful', [
                    'employee_id' => $employee->employee_id,
                    'user_id' => $employee->user_id,
                    'face_id' => $faceId,
                    'enrolled_by' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment wajah berhasil! Karyawan dapat melakukan presensi.',
                    'face_id' => $faceId,
                    'data' => [
                        'employee_name' => $employee->user->name,
                        'employee_id' => $employee->employee_id,
                        'enrolled_at' => now()->format('d/m/Y H:i:s')
                    ]
                ]);
            } else {
                Log::warning('Face enrollment failed', [
                    'employee_id' => $employee->employee_id,
                    'api_response' => $result,
                    'status_message' => $statusMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $statusMessage
                ], 422);
            }

        } catch (Exception $e) {
            Log::error('Face enrollment error: ' . $e->getMessage(), [
                'employee_id' => $employee->employee_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle specific API errors
            $errorMessage = $this->getErrorMessage($e);

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Re-enroll face for existing user
     */
    public function reenroll(Request $request, Employee $employee)
    {
        if (!$employee->user->hasFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan belum pernah enrollment sebelumnya. Gunakan enrollment biasa.'
            ], 422);
        }

        try {
            // Delete existing face first
            $oldFaceId = $employee->user->face_id;

            try {
                $this->faceService->deleteFace($oldFaceId);
                Log::info('Old face deleted for re-enrollment', [
                    'employee_id' => $employee->employee_id,
                    'old_face_id' => $oldFaceId
                ]);
            } catch (Exception $e) {
                Log::warning('Failed to delete old face, continuing with re-enrollment', [
                    'employee_id' => $employee->employee_id,
                    'old_face_id' => $oldFaceId,
                    'error' => $e->getMessage()
                ]);
            }

            // Clear face_id
            $employee->user->update(['face_id' => null]);

            // Proceed with new enrollment
            return $this->enroll($request, $employee);

        } catch (Exception $e) {
            Log::error('Face re-enrollment error: ' . $e->getMessage(), [
                'employee_id' => $employee->employee_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan re-enrollment: ' . $this->getErrorMessage($e)
            ], 500);
        }
    }

    /**
     * Delete face enrollment
     */
    public function delete(Employee $employee)
    {
        if (!$employee->user->hasFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan belum enrollment wajah.'
            ], 422);
        }

        try {
            $faceId = $employee->user->face_id;

            // Delete from Biznet API
            $this->faceService->deleteFace($faceId);

            // Clear face_id from user
            $employee->user->update(['face_id' => null]);

            Log::info('Face enrollment deleted', [
                'employee_id' => $employee->employee_id,
                'face_id' => $faceId,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data wajah berhasil dihapus.',
                'data' => [
                    'employee_name' => $employee->user->name,
                    'employee_id' => $employee->employee_id
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Face deletion error: ' . $e->getMessage(), [
                'employee_id' => $employee->employee_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data wajah: ' . $this->getErrorMessage($e)
            ], 500);
        }
    }

    /**
     * Test face verification
     */
    public function testVerification(Request $request, Employee $employee)
    {
        if (!$employee->user->hasFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan belum enrollment wajah.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Foto test wajib diisi.'
            ], 422);
        }

        try {
            // Validate and process image
            $imageValidation = $this->imageService->validateBase64Image($request->photo);
            if (!$imageValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak valid: ' . $imageValidation['error']
                ], 422);
            }

            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Call verify API
            $result = $this->faceService->verifyFace($employee->user->face_id, $base64Photo);

            // The service now handles risetai wrapper extraction, so we can use the result directly
            $similarity = ($result['similarity'] ?? 0) * 100;
            $threshold = config('services.biznet_face.similarity_threshold', 0.75) * 100;
            $verified = $result['verified'] ?? false;

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => $verified ?
                    "✓ Verifikasi berhasil! Similarity: {$similarity}% (threshold: {$threshold}%)" :
                    "✗ Verifikasi gagal. Similarity: {$similarity}% (threshold: {$threshold}%)",
                'data' => [
                    'verified' => $verified,
                    'similarity' => $similarity,
                    'threshold' => $threshold,
                    'masker' => $result['masker'] ?? $result['mask'] ?? false,
                    'user_name' => $result['user_name'] ?? $employee->user->name
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Face verification test error: ' . $e->getMessage(), [
                'employee_id' => $employee->employee_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal test verifikasi: ' . $this->getErrorMessage($e)
            ], 500);
        }
    }

    /**
     * Get enrollment statistics
     */
    public function stats()
    {
        $stats = $this->getEnrollmentStats();

        // Get API counters
        try {
            $apiCounters = $this->faceService->getCounters();
        } catch (Exception $e) {
            $apiCounters = ['error' => 'API tidak tersedia: ' . $e->getMessage()];
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'api_counters' => $apiCounters
        ]);
    }

    /**
     * List enrolled faces from API
     */
    public function listEnrolledFaces()
    {
        try {
            $result = $this->faceService->listFaces();

            return response()->json([
                'success' => true,
                'faces' => $result['faces'] ?? []
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar wajah: ' . $this->getErrorMessage($e)
            ], 500);
        }
    }

    /**
     * Bulk enrollment for multiple employees
     */
    public function bulkEnroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . $validator->errors()->first()
            ], 422);
        }

        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::find($employeeId);

            if (!$employee || $employee->user->hasFaceEnrolled()) {
                $results[] = [
                    'employee_id' => $employee?->employee_id ?? $employeeId,
                    'name' => $employee?->user->name ?? 'Unknown',
                    'status' => 'skipped',
                    'message' => 'Sudah enrolled atau tidak ditemukan'
                ];
                continue;
            }

            // For bulk enrollment, we need photos to be uploaded separately
            // This is just a placeholder for the functionality
            $results[] = [
                'employee_id' => $employee->employee_id,
                'name' => $employee->user->name,
                'status' => 'pending',
                'message' => 'Menunggu foto untuk enrollment'
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Proses bulk enrollment dimulai. {$successCount} berhasil, {$failCount} gagal.",
            'results' => $results
        ]);
    }

    /**
     * Export enrollment data
     */
    public function export(Request $request)
    {
        $employees = Employee::with(['user', 'location'])
            ->active()
            ->get();

        $filename = 'face_enrollment_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function() use ($employees) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Employee ID',
                'Name',
                'Email',
                'Department',
                'Position',
                'Location',
                'Face Enrolled',
                'Face ID',
                'Status'
            ]);

            // Data rows
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_id,
                    $employee->user->name,
                    $employee->user->email,
                    $employee->department,
                    $employee->position,
                    $employee->location->name,
                    $employee->user->hasFaceEnrolled() ? 'Yes' : 'No',
                    $employee->user->face_id ?? '-',
                    ucfirst($employee->status)
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Get enrollment statistics
     */
    private function getEnrollmentStats(): array
    {
        $totalEmployees = Employee::active()->count();
        $enrolledEmployees = User::whereNotNull('face_id')
            ->whereHas('employee', function($q) {
                $q->where('status', 'active');
            })
            ->count();

        $pendingEmployees = $totalEmployees - $enrolledEmployees;
        $enrollmentRate = $totalEmployees > 0 ? round(($enrolledEmployees / $totalEmployees) * 100, 2) : 0;

        return [
            'total_employees' => $totalEmployees,
            'enrolled_employees' => $enrolledEmployees,
            'pending_employees' => $pendingEmployees,
            'enrollment_rate' => $enrollmentRate,
            'departments' => Employee::selectRaw('department, COUNT(*) as total, SUM(CASE WHEN users.face_id IS NOT NULL THEN 1 ELSE 0 END) as enrolled')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->where('employees.status', 'active')
                ->groupBy('department')
                ->get()
                ->map(function($item) {
                    return [
                        'department' => $item->department,
                        'total' => $item->total,
                        'enrolled' => $item->enrolled,
                        'rate' => $item->total > 0 ? round(($item->enrolled / $item->total) * 100, 1) : 0
                    ];
                })
        ];
    }

    /**
     * Get user-friendly error message
     */
    private function getErrorMessage(Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Face not detected')) {
            return 'Wajah tidak terdeteksi pada foto. Pastikan wajah terlihat jelas dan pencahayaan cukup.';
        }

        if (str_contains($message, 'Face too small')) {
            return 'Wajah terlalu kecil. Dekatkan kamera ke wajah.';
        }

        if (str_contains($message, 'quota') || str_contains($message, 'limit')) {
            return 'Kuota API Face Recognition habis. Hubungi administrator.';
        }

        if (str_contains($message, 'not found')) {
            return 'Data wajah tidak ditemukan dalam sistem.';
        }

        if (str_contains($message, 'token') || str_contains($message, 'unauthorized')) {
            return 'Token API tidak valid. Hubungi administrator.';
        }

        return 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.';
    }
}
