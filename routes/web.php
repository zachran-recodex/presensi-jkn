<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FaceEnrollmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Attendance routes - Updated with flexible access
Route::middleware(['auth'])->group(function () {
    // Attendance form and history (accessible by both admin and employees)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    // Photo routes (with authorization check inside controller)
    Route::get('/attendance/{attendance}/photo', [AttendanceController::class, 'getPhoto'])->name('attendance.photo');
    Route::get('/attendance/{attendance}/thumbnail', [AttendanceController::class, 'getThumbnail'])->name('attendance.thumbnail');

    // Clock in/out only for employees with proper checks
    Route::middleware(['employee'])->group(function () {
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    });

    // Real-time attendance stats (admin only)
    Route::get('/attendance/realtime-stats', [AttendanceController::class, 'getRealtimeStats'])->name('attendance.realtime-stats');
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Employee management
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');

    // Location management
    Route::resource('locations', LocationController::class);
    Route::post('/locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
    Route::post('/locations/validate-coordinates', [LocationController::class, 'validateCoordinates'])->name('locations.validate-coordinates');
    Route::post('/locations/calculate-distance', [LocationController::class, 'calculateDistance'])->name('locations.calculate-distance');

    // Face enrollment management
    Route::get('/face-enrollment', [FaceEnrollmentController::class, 'index'])->name('face-enrollment.index');
    Route::get('/face-enrollment/{employee}', [FaceEnrollmentController::class, 'show'])->name('face-enrollment.show');
    Route::post('/face-enrollment/{employee}/enroll', [FaceEnrollmentController::class, 'enroll'])->name('face-enrollment.enroll');
    Route::post('/face-enrollment/{employee}/reenroll', [FaceEnrollmentController::class, 'reenroll'])->name('face-enrollment.reenroll');
    Route::delete('/face-enrollment/{employee}/delete', [FaceEnrollmentController::class, 'delete'])->name('face-enrollment.delete');
    Route::post('/face-enrollment/{employee}/test', [FaceEnrollmentController::class, 'testVerification'])->name('face-enrollment.test');
    Route::get('/face-enrollment/stats', [FaceEnrollmentController::class, 'stats'])->name('face-enrollment.stats');
    Route::get('/face-enrollment/list-faces', [FaceEnrollmentController::class, 'listEnrolledFaces'])->name('face-enrollment.list-faces');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/export/monthly', [ReportController::class, 'exportMonthly'])->name('reports.export.monthly');

    // Admin attendance history
    Route::get('/admin/attendance-history', [AttendanceController::class, 'history'])->name('admin.attendance.history');
    Route::get('/admin/attendance-stats', [AttendanceController::class, 'getRealtimeStats'])->name('admin.attendance.stats');

    // Face API Testing routes
    Route::prefix('face-api-test')->name('face-api-test.')->group(function () {
        Route::get('/', [App\Http\Controllers\FaceApiTestController::class, 'index'])->name('index');
        Route::post('/connection', [App\Http\Controllers\FaceApiTestController::class, 'testConnection'])->name('connection');
        Route::post('/counters', [App\Http\Controllers\FaceApiTestController::class, 'getCounters'])->name('counters');
        Route::post('/galleries', [App\Http\Controllers\FaceApiTestController::class, 'getMyFaceGalleries'])->name('galleries');
        Route::post('/gallery/create', [App\Http\Controllers\FaceApiTestController::class, 'createFaceGallery'])->name('gallery.create');
        Route::post('/gallery/delete', [App\Http\Controllers\FaceApiTestController::class, 'deleteFaceGallery'])->name('gallery.delete');
        Route::post('/enroll', [App\Http\Controllers\FaceApiTestController::class, 'testEnrollFace'])->name('enroll');
        Route::post('/verify', [App\Http\Controllers\FaceApiTestController::class, 'testVerifyFace'])->name('verify');
        Route::post('/identify', [App\Http\Controllers\FaceApiTestController::class, 'testIdentifyFace'])->name('identify');
        Route::post('/compare', [App\Http\Controllers\FaceApiTestController::class, 'testCompareImages'])->name('compare');
        Route::post('/faces/list', [App\Http\Controllers\FaceApiTestController::class, 'listFaces'])->name('faces.list');
        Route::post('/faces/delete', [App\Http\Controllers\FaceApiTestController::class, 'deleteFace'])->name('faces.delete');
        Route::get('/error-message', [App\Http\Controllers\FaceApiTestController::class, 'getErrorMessage'])->name('error.message');
    });
});

// Employee individual report (accessible by employee and admin)
Route::middleware('auth')->group(function () {
    Route::get('/reports/employee/{employee}', [ReportController::class, 'employee'])
        ->name('reports.employee')
        ->middleware('can:view,employee');
});

// Debug route (REMOVE AFTER DEBUGGING)
Route::middleware(['auth'])->get('/debug-attendance', function () {
    $user = auth()->user();

    $debug = [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_role' => $user->role,
        'user_is_active' => $user->is_active,
        'has_employee' => $user->employee ? 'YES' : 'NO',
    ];

    if ($user->employee) {
        $debug['employee_id'] = $user->employee->employee_id;
        $debug['employee_status'] = $user->employee->status;
        $debug['employee_location'] = $user->employee->location->name ?? 'No Location';
        $debug['face_enrolled'] = $user->hasFaceEnrolled() ? 'YES' : 'NO';
        $debug['face_id'] = $user->face_id ?? 'NULL';
    }

    $debug['middleware_checks'] = [
        'is_authenticated' => auth()->check(),
        'has_employee_profile' => $user->employee ? true : false,
        'employee_is_active' => $user->employee ? ($user->employee->status === 'active') : false,
        'user_is_active' => $user->is_active,
        'can_access_attendance' => $user->employee &&
                                  $user->employee->status === 'active' &&
                                  $user->is_active
    ];

    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
})->name('debug.attendance');

require __DIR__.'/auth.php';
