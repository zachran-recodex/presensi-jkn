# Integration Guide: Face API Testing

## 📋 Setup Instructions

### 1. **Update Routes**

Add to `routes/web.php` after existing admin routes:

```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Existing admin routes...
    
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
```

### 2. **Register Artisan Command**

Add to `app/Console/Kernel.php`:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\FaceApiTestCommand::class,  // Add this line
    ];

    // ... rest of the class
}
```

### 3. **Create Views Directory**

```bash
mkdir -p resources/views/face-api-test
```

### 4. **Add Menu Link to Admin Dashboard**

Add to your admin navigation (usually in `resources/views/layouts/app.blade.php` or similar):

```php
@if(auth()->user()->isAdmin())
<li class="nav-item">
    <a class="nav-link" href="{{ route('face-api-test.index') }}">
        <i class="fas fa-robot"></i>
        <span>Face API Testing</span>
    </a>
</li>
@endif
```

### 5. **Environment Configuration**

Ensure `.env` has correct Biznet Face API settings:

```env
# Biznet Face Recognition API Configuration
BIZNET_FACE_BASE_URL=https://fr.neoapi.id/risetai/face-api
BIZNET_FACE_ACCESS_TOKEN=your_actual_token_here
BIZNET_FACE_GALLERY_ID=jakakuasa.production
BIZNET_FACE_SIMILARITY_THRESHOLD=0.75
BIZNET_FACE_TIMEOUT=30
```

## 🚀 Usage Examples

### Web Interface Testing

1. **Access the testing page:**
   ```
   https://yourdomain.com/face-api-test
   ```

2. **Test API Connection:**
   - Click "Test Connection" button
   - Verify green success message
   - Check API quota in response

3. **Test Face Enrollment:**
   - Go to "Face Enrollment" tab
   - Click "Start Camera" and capture a clear face photo
   - Fill User ID: `test-user-001`
   - Fill User Name: `Test User`
   - Click "Enroll Face"

4. **Test Face Verification:**
   - Go to "Face Verification" tab
   - Capture the same person's face
   - Enter User ID: `test-user-001`
   - Click "Verify Face"
   - Check similarity score (should be >75%)

### Command Line Testing

1. **Test API Connection:**
   ```bash
   php artisan face-api:test --setup
   ```

2. **Check API Quota:**
   ```bash
   php artisan face-api:test --counters
   ```

3. **List Galleries:**
   ```bash
   php artisan face-api:test --galleries
   ```

4. **Create Test Gallery:**
   ```bash
   php artisan face-api:test --create-gallery=test-gallery-2024
   ```

5. **Enroll Face from Image:**
   ```bash
   php artisan face-api:test --enroll=/path/to/face.jpg --gallery=test-gallery-2024
   ```

6. **Verify Face:**
   ```bash
   php artisan face-api:test --verify=/path/to/face.jpg --gallery=test-gallery-2024
   ```

## 🔧 Troubleshooting Common Issues

### Issue: "Class FaceApiTestController not found"

**Solution:**
```bash
composer dump-autoload
php artisan route:clear
php artisan config:clear
```

### Issue: "Camera not working in browser"

**Solutions:**
1. Use HTTPS (required for production)
2. Allow camera permissions in browser
3. Check if other applications are using camera
4. Try different browser

### Issue: "Token not authorized"

**Solutions:**
1. Verify token in Biznet Portal
2. Check if service is still active
3. Ensure no extra spaces in .env file
4. Restart Laravel server after .env changes

### Issue: "Face not detected"

**Solutions:**
1. Improve lighting conditions
2. Face should be 30-50cm from camera
3. Face should be directly facing camera
4. Remove masks or sunglasses
5. Ensure face takes up 20-30% of image

## 📊 Expected Test Results

### ✅ **Successful API Connection**
```json
{
  "success": true,
  "message": "API connected and default FaceGallery exists",
  "counters": {
    "status": "success",
    "remaining_limit": {
      "n_api_hits": 1000,
      "n_face": 500,
      "n_facegallery": 10
    }
  }
}
```

### ✅ **Successful Face Enrollment**
```json
{
  "success": true,
  "message": "Face enrolled successfully",
  "data": {
    "status": "success",
    "status_message": "Face enrolled successfully"
  }
}
```

### ✅ **Successful Face Verification**
```json
{
  "success": true,
  "message": "Face verification completed",
  "data": {
    "status": "success",
    "verified": true,
    "similarity": 0.95,
    "user_name": "Test User",
    "masker": false
  }
}
```

## 🎯 Testing Checklist

### Pre-deployment Testing

- [ ] ✅ API connection successful
- [ ] ✅ Token valid and quota available
- [ ] ✅ Default gallery created
- [ ] ✅ Face enrollment works with good photos
- [ ] ✅ Face verification accurate (>75% similarity)
- [ ] ✅ Face identification works (1:N)
- [ ] ✅ Image comparison functional
- [ ] ✅ Error handling works for bad photos
- [ ] ✅ Camera access works in browser
- [ ] ✅ All test scenarios pass

### Production Readiness

- [ ] ✅ Production token configured
- [ ] ✅ Production gallery setup
- [ ] ✅ SSL certificate active (for camera)
- [ ] ✅ Performance acceptable (<5 sec response)
- [ ] ✅ Error logging configured
- [ ] ✅ Monitoring alerts setup
- [ ] ✅ Backup/recovery plan ready

## 📞 Support & Resources

### Documentation Links
- [Biznet Face API Docs](https://documenter.getpostman.com/view/16178629/UVsEVpHD)
- [Laravel HTTP Client](https://laravel.com/docs/http-client)
- [WebRTC Camera API](https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia)

### Contact Support
- **Biznet Support:** support@biznetgio.com
- **Portal:** https://portal.biznetgio.com
- **Ticket System:** Available in Biznet Portal

### Useful Commands
```bash
# Clear all Laravel caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check logs
tail -f storage/logs/laravel.log

# Test specific API endpoint
php artisan face-api:test --counters
```

---

## 🎉 Success!

If all tests pass, your Face API integration is ready for production use in the attendance system!

Next steps:
1. Update FaceEnrollmentController to use updated service
2. Test actual employee enrollment
3. Test attendance verification
4. Monitor production usage