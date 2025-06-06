<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\FaceRecognitionService;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class AttendanceController extends Controller
{
    protected $faceService;
    protected $imageService;

    public function __construct(FaceRecognitionService $faceService, ImageProcessingService $imageService)
    {
        $this->faceService = $faceService;
        $this->imageService = $imageService;
    }

    /**
     * Show attendance form
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is admin - redirect to admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.attendance.history')
                ->with('info', 'Admin dapat melihat semua riwayat presensi di halaman ini.');
        }

        // Check if user has employee profile
        if (!$user->employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil karyawan tidak ditemukan. Silakan hubungi admin untuk membuat profil karyawan.');
        }

        $employee = $user->employee;

        // Check if employee is active
        if ($employee->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'Akun karyawan tidak aktif. Status: ' . ucfirst($employee->status) . '. Hubungi admin.');
        }

        // Check if user account is active
        if (!$user->is_active) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun pengguna tidak aktif. Hubungi admin untuk mengaktifkan akun.');
        }

        // Check if face is enrolled
        if (!$user->hasFaceEnrolled()) {
            return redirect()->route('dashboard')
                ->with('warning', 'Wajah belum terdaftar dalam sistem. Silakan hubungi admin untuk melakukan enrollment wajah sebelum dapat melakukan presensi.');
        }

        // Check if location exists
        if (!$employee->location || !$employee->location->is_active) {
            return redirect()->route('dashboard')
                ->with('error', 'Lokasi kerja tidak valid atau tidak aktif. Hubungi admin.');
        }

        $todayClockIn = $user->getTodayClockIn();
        $todayClockOut = $user->getTodayClockOut();

        $canClockIn = !$todayClockIn;
        $canClockOut = $todayClockIn && !$todayClockOut;

        // Get today's work schedule
        $now = Carbon::now();
        $workStartTime = Carbon::parse($employee->work_start_time);
        $workEndTime = Carbon::parse($employee->work_end_time);

        // Calculate if currently late
        $isCurrentlyLate = !$employee->is_flexible_time && $now->greaterThan($workStartTime);
        $lateMinutes = $isCurrentlyLate ? $now->diffInMinutes($workStartTime) : 0;

        return view('attendance.form', compact(
            'employee',
            'todayClockIn',
            'todayClockOut',
            'canClockIn',
            'canClockOut',
            'workStartTime',
            'workEndTime',
            'isCurrentlyLate',
            'lateMinutes'
        ));
    }

    /**
     * Process clock in
     */
    public function clockIn(Request $request)
    {
        return $this->processAttendance($request, 'clock_in');
    }

    /**
     * Process clock out
     */
    public function clockOut(Request $request)
    {
        return $this->processAttendance($request, 'clock_out');
    }

    /**
     * Main attendance processing logic
     */
    private function processAttendance(Request $request, string $type)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            // Validation
            $validator = Validator::make($request->all(), [
                'photo' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid: ' . $validator->errors()->first()
                ], 422);
            }

            // Check if employee is active
            if (!$employee || $employee->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun karyawan tidak aktif.'
                ], 403);
            }

            // Check duplicate attendance
            if ($this->isDuplicateAttendance($user->id, $type)) {
                $message = $type === 'clock_in' ? 'Anda sudah melakukan clock in hari ini.' : 'Anda sudah melakukan clock out hari ini.';
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            // Validate location
            $locationValidation = $this->validateLocation(
                $request->latitude,
                $request->longitude,
                $employee->location
            );

            // Prepare attendance data
            $attendanceData = [
                'user_id' => $user->id,
                'location_id' => $employee->location_id,
                'type' => $type,
                'attendance_date' => Carbon::today(),
                'attendance_time' => Carbon::now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_valid_location' => $locationValidation['is_valid'],
                'distance_from_office' => $locationValidation['distance'],
                'device_info' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'notes' => $request->notes,
                'status' => 'pending'
            ];

            // Calculate late status for clock_in
            if ($type === 'clock_in' && !$employee->is_flexible_time) {
                $workStartTime = Carbon::parse($employee->work_start_time);
                $currentTime = Carbon::now();

                if ($currentTime->greaterThan($workStartTime)) {
                    $attendanceData['is_late'] = true;
                    $attendanceData['late_minutes'] = $currentTime->diffInMinutes($workStartTime);
                }
            }

            // Process face verification
            $faceResult = $this->processFaceVerification($request->photo, $user->face_id);

            $attendanceData['face_recognition_result'] = $faceResult;
            $attendanceData['face_similarity_score'] = $faceResult['similarity'] ?? 0;

            // Determine final status
            $similarityThreshold = config('services.biznet_face.similarity_threshold', 0.75);
            $isFaceValid = ($faceResult['verified'] ?? false) &&
                ($faceResult['similarity'] ?? 0) >= $similarityThreshold;

            if ($isFaceValid && $locationValidation['is_valid']) {
                $attendanceData['status'] = 'success';
            } else {
                $attendanceData['status'] = 'failed';
                $reasons = [];

                if (!$isFaceValid) {
                    $reasons[] = 'Verifikasi wajah gagal (similarity: ' .
                        number_format(($faceResult['similarity'] ?? 0) * 100, 1) . '%)';
                }
                if (!$locationValidation['is_valid']) {
                    $reasons[] = 'Lokasi di luar jangkauan kantor (' .
                        number_format($locationValidation['distance'], 0) . 'm)';
                }

                $attendanceData['failure_reason'] = implode(', ', $reasons);
            }

            // Save photo using ImageProcessingService
            $photoResult = $this->imageService->saveAttendancePhoto(
                $request->photo,
                $user->id,
                $type
            );

            if ($photoResult['success']) {
                $attendanceData['photo_path'] = $photoResult['path'];
            } else {
                Log::error('Photo save failed: ' . $photoResult['error']);
                // Continue without photo - don't fail the entire process
            }

            // Create attendance record
            $attendance = Attendance::create($attendanceData);

            // Prepare response
            $message = $attendanceData['status'] === 'success'
                ? ($type === 'clock_in' ? 'Clock in berhasil!' : 'Clock out berhasil!')
                : 'Presensi gagal: ' . ($attendanceData['failure_reason'] ?? 'Kesalahan tidak diketahui');

            return response()->json([
                'success' => $attendanceData['status'] === 'success',
                'message' => $message,
                'data' => [
                    'attendance_id' => $attendance->id,
                    'time' => $attendance->attendance_time->format('H:i:s'),
                    'location_valid' => $locationValidation['is_valid'],
                    'distance' => $locationValidation['distance'],
                    'face_verified' => $isFaceValid,
                    'similarity_score' => number_format(($faceResult['similarity'] ?? 0) * 100, 1),
                    'is_late' => $attendanceData['is_late'] ?? false,
                    'late_minutes' => $attendanceData['late_minutes'] ?? 0,
                    'photo_saved' => $photoResult['success'] ?? false
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Attendance processing error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'type' => $type,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check for duplicate attendance
     */
    private function isDuplicateAttendance(int $userId, string $type): bool
    {
        return Attendance::where('user_id', $userId)
            ->where('type', $type)
            ->where('attendance_date', Carbon::today())
            ->where('status', 'success')
            ->exists();
    }

    /**
     * Validate user location against office location
     */
    private function validateLocation(float $userLat, float $userLng, $officeLocation): array
    {
        $distance = $officeLocation->getDistanceFrom($userLat, $userLng);
        $isValid = $distance <= $officeLocation->radius;

        return [
            'is_valid' => $isValid,
            'distance' => round($distance, 2)
        ];
    }

    /**
     * Process face verification with Biznet API
     */
    private function processFaceVerification(string $base64Photo, string $faceId): array
    {
        try {
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64Photo);

            // Validate image using ImageProcessingService
            $validation = $this->imageService->validateBase64Image($base64Photo);
            if (!$validation['valid']) {
                return [
                    'verified' => false,
                    'similarity' => 0,
                    'error' => $validation['error']
                ];
            }

            // Call Biznet Face API
            $result = $this->faceService->verifyFace($faceId, $base64Photo);

            // ADD DEBUG LOGGING
            Log::info('Face Verification API Response', [
                'face_id' => $faceId,
                'raw_response' => $result,
                'response_keys' => array_keys($result),
                'similarity_value' => $result['similarity'] ?? 'NOT_FOUND',
                'confidence_level' => $result['confidence_level'] ?? 'NOT_FOUND',
                'verified' => $result['verified'] ?? 'NOT_FOUND'
            ]);

            // Check for different possible field names
            $similarity = 0;
            
            // Try different field names that API might use
            if (isset($result['similarity'])) {
                $similarity = $result['similarity'];
            } elseif (isset($result['confidence_level'])) {
                $similarity = $result['confidence_level'];
            } elseif (isset($result['confidence'])) {
                $similarity = $result['confidence'];
            } elseif (isset($result['score'])) {
                $similarity = $result['score'];
            }

            // Convert to decimal if it's percentage (0-100 to 0-1)
            if ($similarity > 1) {
                $similarity = $similarity / 100;
            }

            // Normalize response
            $normalizedResult = [
                'verified' => $result['verified'] ?? false,
                'similarity' => $similarity,
                'masker' => $result['masker'] ?? $result['mask'] ?? false,
                'status' => $result['status'] ?? '',
                'status_message' => $result['status_message'] ?? '',
                'user_name' => $result['user_name'] ?? ''
            ];

            // ADD DEBUG LOGGING FOR NORMALIZED RESULT
            Log::info('Face Verification Normalized Result', [
                'face_id' => $faceId,
                'normalized_result' => $normalizedResult,
                'original_similarity' => $result['similarity'] ?? 'NOT_FOUND',
                'final_similarity' => $similarity
            ]);

            return $normalizedResult;

        } catch (Exception $e) {
            Log::error('Face verification error: ' . $e->getMessage(), [
                'face_id' => $faceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'verified' => false,
                'similarity' => 0,
                'error' => 'Gagal memverifikasi wajah: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Show attendance history
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminHistory($request);
        }

        return $this->userHistory($request);
    }

    /**
     * User's own attendance history
     */
    private function userHistory(Request $request)
    {
        $user = Auth::user();

        $query = Attendance::where('user_id', $user->id)
            ->with('location')
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc');

        // Date filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        // Type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20);

        // Calculate monthly summary
        $currentMonth = Carbon::now();
        $monthlySummary = [
            'total_days' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->whereYear('attendance_date', $currentMonth->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->distinct('attendance_date')
                ->count(),
            'late_days' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->whereYear('attendance_date', $currentMonth->year)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->where('is_late', true)
                ->count()
        ];

        return view('attendance.history', compact('attendances', 'monthlySummary'));
    }

    /**
     * Admin view of all attendance history
     */
    private function adminHistory(Request $request)
    {
        $query = Attendance::with(['user', 'location'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc');

        // Employee filter
        if ($request->has('employee') && $request->employee) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee . '%')
                  ->orWhereHas('employee', function($eq) use ($request) {
                      $eq->where('employee_id', 'like', '%' . $request->employee . '%');
                  });
            });
        }

        // Date filters
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Location filter
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        $attendances = $query->paginate(20);

        // Get filter options
        $locations = \App\Models\Location::active()->get();

        return view('attendance.admin-history', compact('attendances', 'locations'));
    }

    /**
     * Get attendance photo
     */
    public function getPhoto(Attendance $attendance)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && Auth::id() !== $attendance->user_id) {
            abort(403);
        }

        if (!$attendance->photo_path) {
            abort(404, 'Photo not found');
        }

        $photoUrl = $this->imageService->getImageUrl($attendance->photo_path);
        if (!$photoUrl) {
            abort(404, 'Photo file not found');
        }

        return redirect($photoUrl);
    }

    /**
     * Get attendance thumbnail
     */
    public function getThumbnail(Attendance $attendance)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && Auth::id() !== $attendance->user_id) {
            abort(403);
        }

        if (!$attendance->photo_path) {
            abort(404, 'Photo not found');
        }

        $thumbnailUrl = $this->imageService->getImageUrl($attendance->photo_path, true);
        if (!$thumbnailUrl) {
            // Fallback to original photo
            $photoUrl = $this->imageService->getImageUrl($attendance->photo_path);
            if (!$photoUrl) {
                abort(404, 'Photo file not found');
            }
            return redirect($photoUrl);
        }

        return redirect($thumbnailUrl);
    }

    /**
     * API endpoint for real-time attendance stats
     */
    public function getRealtimeStats()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();

        $stats = [
            'today_present' => Attendance::whereDate('attendance_date', $today)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->count(),
            'today_late' => Attendance::whereDate('attendance_date', $today)
                ->where('type', 'clock_in')
                ->where('status', 'success')
                ->where('is_late', true)
                ->count(),
            'today_failed' => Attendance::whereDate('attendance_date', $today)
                ->where('status', 'failed')
                ->count(),
            'last_attendances' => Attendance::with(['user'])
                ->whereDate('attendance_date', $today)
                ->where('status', 'success')
                ->orderBy('attendance_time', 'desc')
                ->limit(5)
                ->get()
                ->map(function($attendance) {
                    return [
                        'user_name' => $attendance->user->name,
                        'type' => $attendance->type,
                        'time' => $attendance->attendance_time->format('H:i'),
                        'is_late' => $attendance->is_late
                    ];
                }),
            'updated_at' => now()->format('H:i:s')
        ];

        return response()->json($stats);
    }
}
