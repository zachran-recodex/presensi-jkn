<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class AttendanceController extends Controller
{
    protected $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    /**
     * Show attendance form
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee || $employee->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'Akun karyawan tidak aktif atau belum terdaftar.');
        }

        if (!$user->hasFaceEnrolled()) {
            return redirect()->route('dashboard')
                ->with('error', 'Wajah belum terdaftar. Silakan hubungi admin untuk enrollment.');
        }

        $todayClockIn = $user->getTodayClockIn();
        $todayClockOut = $user->getTodayClockOut();

        $canClockIn = !$todayClockIn;
        $canClockOut = $todayClockIn && !$todayClockOut;

        return view('attendance.form', compact(
            'employee',
            'todayClockIn',
            'todayClockOut',
            'canClockIn',
            'canClockOut'
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
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
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
            $isFaceValid = ($faceResult['verified'] ?? false) &&
                ($faceResult['similarity'] ?? 0) >= config('services.biznet_face.similarity_threshold', 0.75);

            if ($isFaceValid && $locationValidation['is_valid']) {
                $attendanceData['status'] = 'success';
            } else {
                $attendanceData['status'] = 'failed';
                $reasons = [];

                if (!$isFaceValid) {
                    $reasons[] = 'Verifikasi wajah gagal';
                }
                if (!$locationValidation['is_valid']) {
                    $reasons[] = 'Lokasi di luar jangkauan kantor';
                }

                $attendanceData['failure_reason'] = implode(', ', $reasons);
            }

            // Save photo
            $photoPath = $this->saveAttendancePhoto($request->photo, $user->id, $type);
            $attendanceData['photo_path'] = $photoPath;

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
                    'face_verified' => $isFaceValid,
                    'similarity_score' => $faceResult['similarity'] ?? 0,
                    'is_late' => $attendanceData['is_late'] ?? false,
                    'late_minutes' => $attendanceData['late_minutes'] ?? 0
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Attendance processing error: ' . $e->getMessage());

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

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return [
                    'verified' => false,
                    'similarity' => 0,
                    'error' => 'Format gambar tidak valid'
                ];
            }

            // Call Biznet Face API
            $result = $this->faceService->verifyFace($faceId, $base64Photo);

            return [
                'verified' => $result['verified'] ?? false,
                'similarity' => $result['similarity'] ?? 0,
                'masker' => $result['masker'] ?? false,
                'status' => $result['status'] ?? '',
                'status_message' => $result['status_message'] ?? ''
            ];

        } catch (Exception $e) {
            Log::error('Face verification error: ' . $e->getMessage());

            return [
                'verified' => false,
                'similarity' => 0,
                'error' => 'Gagal memverifikasi wajah: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Save attendance photo to storage
     */
    private function saveAttendancePhoto(string $base64Photo, int $userId, string $type): string
    {
        try {
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64Photo);

            // Generate filename
            $date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now()->format('His');
            $filename = "attendance/{$date}/user_{$userId}_{$type}_{$time}.jpg";

            // Decode and save
            $imageData = base64_decode($base64Photo);
            Storage::disk('public')->put($filename, $imageData);

            return $filename;

        } catch (Exception $e) {
            Log::error('Photo save error: ' . $e->getMessage());
            throw new Exception('Gagal menyimpan foto presensi');
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

        $attendances = $query->paginate(20);

        return view('attendance.history', compact('attendances'));
    }

    /**
     * Admin view of all attendance history
     */
    private function adminHistory(Request $request)
    {
        $query = Attendance::with(['user', 'location'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc');

        // Filters
        if ($request->has('employee') && $request->employee) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee . '%');
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20);

        return view('attendance.admin-history', compact('attendances'));
    }
}
