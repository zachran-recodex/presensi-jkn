<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class FaceEnrollmentController extends Controller
{
    protected $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->middleware(['auth', 'admin']);
        $this->faceService = $faceService;
    }

    /**
     * Show face enrollment dashboard
     */
    public function index()
    {
        $employees = Employee::with('user')
            ->active()
            ->paginate(20);

        // Get API counters
        try {
            $apiCounters = $this->faceService->getCounters();
        } catch (Exception $e) {
            $apiCounters = ['error' => 'Tidak dapat mengambil data API'];
        }

        return view('face-enrollment.index', compact('employees', 'apiCounters'));
    }

    /**
     * Show enrollment form for specific employee
     */
    public function show(Employee $employee)
    {
        return view('face-enrollment.form', compact('employee'));
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
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak valid. Gunakan JPG atau PNG.'
                ], 422);
            }

            // Generate unique face ID
            $faceId = 'emp_' . $employee->employee_id . '_' . time();

            // Call Biznet Face API
            $result = $this->faceService->enrollFace(
                $faceId,
                $employee->user->name,
                $base64Photo
            );

            if ($result['status'] === 'success') {
                // Update user with face_id
                $employee->user->update(['face_id' => $faceId]);

                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment wajah berhasil! Karyawan dapat melakukan presensi.',
                    'face_id' => $faceId
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment gagal: ' . ($result['status_message'] ?? 'Kesalahan tidak diketahui')
                ], 422);
            }

        } catch (Exception $e) {
            Log::error('Face enrollment error: ' . $e->getMessage());

            // Handle specific API errors
            $errorMessage = 'Terjadi kesalahan sistem.';

            if (str_contains($e->getMessage(), 'Face not detected')) {
                $errorMessage = 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas di foto.';
            } elseif (str_contains($e->getMessage(), 'Face too small')) {
                $errorMessage = 'Wajah terlalu kecil. Dekatkan kamera ke wajah.';
            } elseif (str_contains($e->getMessage(), 'quota')) {
                $errorMessage = 'Kuota API Face Recognition habis. Hubungi administrator.';
            }

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
                'message' => 'Karyawan belum pernah enrollment sebelumnya.'
            ], 422);
        }

        try {
            // Delete existing face first
            $this->faceService->deleteFace($employee->user->face_id);

            // Clear face_id
            $employee->user->update(['face_id' => null]);

            // Proceed with new enrollment
            return $this->enroll($request, $employee);

        } catch (Exception $e) {
            Log::error('Face re-enrollment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan re-enrollment: ' . $e->getMessage()
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
            // Delete from Biznet API
            $this->faceService->deleteFace($employee->user->face_id);

            // Clear face_id from user
            $employee->user->update(['face_id' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Data wajah berhasil dihapus.'
            ]);

        } catch (Exception $e) {
            Log::error('Face deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data wajah: ' . $e->getMessage()
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
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format gambar tidak valid.'
                ], 422);
            }

            // Call verify API
            $result = $this->faceService->verifyFace($employee->user->face_id, $base64Photo);

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => $result['verified'] ?
                    'Verifikasi berhasil! Similarity: ' . number_format($result['similarity'] * 100, 2) . '%' :
                    'Verifikasi gagal. Similarity: ' . number_format($result['similarity'] * 100, 2) . '%'
            ]);

        } catch (Exception $e) {
            Log::error('Face verification test error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal test verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk face enrollment
     */
    public function bulkEnroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.'
            ], 422);
        }

        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::find($employeeId);

            if ($employee->user->hasFaceEnrolled()) {
                $results[] = [
                    'employee' => $employee->user->name,
                    'status' => 'skipped',
                    'message' => 'Sudah enrollment sebelumnya'
                ];
                continue;
            }

            $results[] = [
                'employee' => $employee->user->name,
                'status' => 'pending',
                'message' => 'Perlu foto wajah manual'
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk enrollment memerlukan foto wajah manual untuk setiap karyawan.',
            'results' => $results
        ]);
    }

    /**
     * Get enrollment statistics
     */
    public function stats()
    {
        $totalEmployees = Employee::active()->count();
        $enrolledEmployees = User::whereNotNull('face_id')
            ->whereHas('employee', function($q) {
                $q->where('status', 'active');
            })
            ->count();

        $pendingEmployees = $totalEmployees - $enrolledEmployees;
        $enrollmentRate = $totalEmployees > 0 ? round(($enrolledEmployees / $totalEmployees) * 100, 2) : 0;

        try {
            $apiCounters = $this->faceService->getCounters();
        } catch (Exception $e) {
            $apiCounters = ['error' => 'API tidak tersedia'];
        }

        return response()->json([
            'total_employees' => $totalEmployees,
            'enrolled_employees' => $enrolledEmployees,
            'pending_employees' => $pendingEmployees,
            'enrollment_rate' => $enrollmentRate,
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
                'message' => 'Gagal mengambil daftar wajah: ' . $e->getMessage()
            ], 500);
        }
    }
}
