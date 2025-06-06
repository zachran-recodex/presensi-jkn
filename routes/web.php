<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FaceEnrollmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FaceApiTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| 
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Root redirect to login
Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Profile Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Attendance Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // General attendance routes (accessible by both admin and employees)
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        
        // Photo routes (with authorization check inside controller)
        Route::get('/{attendance}/photo', [AttendanceController::class, 'getPhoto'])->name('photo');
        Route::get('/{attendance}/thumbnail', [AttendanceController::class, 'getThumbnail'])->name('thumbnail');
        
        // Real-time attendance stats
        Route::get('/realtime-stats', [AttendanceController::class, 'getRealtimeStats'])->name('realtime-stats');
        
        // Employee-only attendance actions
        Route::middleware(['employee'])->group(function () {
            Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
            Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Individual Employee Reports
    |--------------------------------------------------------------------------
    */
    Route::get('/reports/employee/{employee}', [ReportController::class, 'employee'])
        ->name('reports.employee')
        ->middleware('can:view,employee');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Employee Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // Additional employee actions
        Route::post('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/export', [EmployeeController::class, 'export'])->name('export');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Location Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/create', [LocationController::class, 'create'])->name('create');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::get('/{location}', [LocationController::class, 'show'])->name('show');
        Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('edit');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        
        // Additional location actions
        Route::post('/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/validate-coordinates', [LocationController::class, 'validateCoordinates'])->name('validate-coordinates');
        Route::post('/calculate-distance', [LocationController::class, 'calculateDistance'])->name('calculate-distance');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Face Enrollment Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('face-enrollment')->name('face-enrollment.')->group(function () {
        Route::get('/', [FaceEnrollmentController::class, 'index'])->name('index');
        Route::get('/stats', [FaceEnrollmentController::class, 'stats'])->name('stats');
        Route::get('/list-faces', [FaceEnrollmentController::class, 'listEnrolledFaces'])->name('list-faces');
        
        // Employee-specific face enrollment routes
        Route::prefix('{employee}')->group(function () {
            Route::get('/', [FaceEnrollmentController::class, 'show'])->name('show');
            Route::post('/enroll', [FaceEnrollmentController::class, 'enroll'])->name('enroll');
            Route::post('/reenroll', [FaceEnrollmentController::class, 'reenroll'])->name('reenroll');
            Route::delete('/delete', [FaceEnrollmentController::class, 'delete'])->name('delete');
            Route::post('/test', [FaceEnrollmentController::class, 'testVerification'])->name('test');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Reports Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/export/monthly', [ReportController::class, 'exportMonthly'])->name('export.monthly');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Admin Attendance Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/attendance-history', [AttendanceController::class, 'history'])->name('attendance.history');
        Route::get('/attendance-stats', [AttendanceController::class, 'getRealtimeStats'])->name('attendance.stats');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Face API Testing Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('face-api-test')->name('face-api-test.')->group(function () {
        Route::get('/', [FaceApiTestController::class, 'index'])->name('index');
        
        // API Connection & Info
        Route::post('/connection', [FaceApiTestController::class, 'testConnection'])->name('connection');
        Route::post('/counters', [FaceApiTestController::class, 'getCounters'])->name('counters');
        Route::get('/error-message', [FaceApiTestController::class, 'getErrorMessage'])->name('error.message');
        
        // Gallery Management
        Route::prefix('gallery')->name('gallery.')->group(function () {
            Route::post('/create', [FaceApiTestController::class, 'createFaceGallery'])->name('create');
            Route::post('/delete', [FaceApiTestController::class, 'deleteFaceGallery'])->name('delete');
        });
        
        // Face Operations
        Route::post('/galleries', [FaceApiTestController::class, 'getMyFaceGalleries'])->name('galleries');
        Route::post('/enroll', [FaceApiTestController::class, 'testEnrollFace'])->name('enroll');
        Route::post('/verify', [FaceApiTestController::class, 'testVerifyFace'])->name('verify');
        Route::post('/identify', [FaceApiTestController::class, 'testIdentifyFace'])->name('identify');
        Route::post('/compare', [FaceApiTestController::class, 'testCompareImages'])->name('compare');
        
        // Face Management
        Route::prefix('faces')->name('faces.')->group(function () {
            Route::post('/list', [FaceApiTestController::class, 'listFaces'])->name('list');
            Route::post('/delete', [FaceApiTestController::class, 'deleteFace'])->name('delete');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';