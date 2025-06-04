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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
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

// Attendance routes (for employees)
Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
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
});

// Admin System Management
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // System Health
    Route::get('/system-health', [App\Http\Controllers\Admin\SystemHealthController::class, 'index'])
        ->name('admin.system-health');
    Route::post('/system-health/fix', [App\Http\Controllers\Admin\SystemHealthController::class, 'fixIssues'])
        ->name('admin.system-health.fix');

    // Photo Cleanup
    Route::get('/photo-cleanup', [App\Http\Controllers\Admin\PhotoCleanupController::class, 'index'])
        ->name('admin.photo-cleanup');
    Route::post('/photo-cleanup/preview', [App\Http\Controllers\Admin\PhotoCleanupController::class, 'preview'])
        ->name('admin.photo-cleanup.preview');
    Route::post('/photo-cleanup/cleanup', [App\Http\Controllers\Admin\PhotoCleanupController::class, 'cleanup'])
        ->name('admin.photo-cleanup.cleanup');

    // Face API Debug
    Route::get('/face-debug', [App\Http\Controllers\Admin\FaceApiDebugController::class, 'index'])
        ->name('admin.face-debug');
    Route::post('/face-debug/test', [App\Http\Controllers\Admin\FaceApiDebugController::class, 'testConnection'])
        ->name('admin.face-debug.test');
    Route::post('/face-debug/operation', [App\Http\Controllers\Admin\FaceApiDebugController::class, 'testFaceOperation'])
        ->name('admin.face-debug.operation');

    // Face Setup
    Route::get('/face-setup', [App\Http\Controllers\Admin\FaceSetupController::class, 'index'])
        ->name('admin.face-setup');
    Route::post('/face-setup/test-connection', [App\Http\Controllers\Admin\FaceSetupController::class, 'testConnection'])
        ->name('admin.face-setup.test');
    Route::post('/face-setup/create-gallery', [App\Http\Controllers\Admin\FaceSetupController::class, 'createGallery'])
        ->name('admin.face-setup.gallery');
    Route::post('/face-setup/validate-config', [App\Http\Controllers\Admin\FaceSetupController::class, 'validateConfig'])
        ->name('admin.face-setup.validate');

    // Data Sync
    Route::get('/data-sync', [App\Http\Controllers\Admin\DataSyncController::class, 'index'])
        ->name('admin.data-sync');
    Route::post('/data-sync/sync', [App\Http\Controllers\Admin\DataSyncController::class, 'sync'])
        ->name('admin.data-sync.sync');
});

// Employee individual report (accessible by employee and admin)
Route::middleware('auth')->group(function () {
    Route::get('/reports/employee/{employee}', [ReportController::class, 'employee'])
        ->name('reports.employee')
        ->middleware('can:view,employee');
});

require __DIR__.'/auth.php';
