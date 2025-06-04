<?php

namespace App\Http\Controllers;

use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class FaceApiTestController extends Controller
{
    protected $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->middleware(['auth', 'admin']);
        $this->faceService = $faceService;
    }

    /**
     * Show Face API testing dashboard
     */
    public function index()
    {
        return view('face-api-test.index');
    }

    /**
     * Test API connection and get counters
     */
    public function testConnection()
    {
        try {
            $result = $this->faceService->testAndSetupAPI();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result
            ]);
        } catch (Exception $e) {
            Log::error('Face API Test Connection Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API counters
     */
    public function getCounters()
    {
        try {
            $counters = $this->faceService->getCounters();

            return response()->json([
                'success' => true,
                'message' => 'Counters retrieved successfully',
                'data' => $counters
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get counters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get my facegalleries
     */
    public function getMyFaceGalleries()
    {
        try {
            $galleries = $this->faceService->getMyFaceGalleries();

            return response()->json([
                'success' => true,
                'message' => 'FaceGalleries retrieved successfully',
                'data' => $galleries
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get facegalleries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new facegallery
     */
    public function createFaceGallery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facegallery_id' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            $result = $this->faceService->createFaceGallery($request->facegallery_id);

            return response()->json([
                'success' => true,
                'message' => 'FaceGallery created successfully',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create facegallery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete facegallery
     */
    public function deleteFaceGallery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facegallery_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            $result = $this->faceService->deleteFaceGallery($request->facegallery_id);

            return response()->json([
                'success' => true,
                'message' => 'FaceGallery deleted successfully',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete facegallery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test face enrollment
     */
    public function testEnrollFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|max:100',
            'user_name' => 'required|string|max:255',
            'photo' => 'required|string',
            'facegallery_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Use JPG or PNG.'
                ], 422);
            }

            $result = $this->faceService->enrollFace(
                $request->user_id,
                $request->user_name,
                $base64Photo,
                $request->facegallery_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Face enrolled successfully',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll face: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test face verification
     */
    public function testVerifyFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'photo' => 'required|string',
            'facegallery_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Use JPG or PNG.'
                ], 422);
            }

            $result = $this->faceService->verifyFace(
                $request->user_id,
                $base64Photo,
                $request->facegallery_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Face verification completed',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify face: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test face identification
     */
    public function testIdentifyFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|string',
            'facegallery_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Clean base64 string
            $base64Photo = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->photo);

            // Validate image
            if (!$this->faceService->validateBase64Image($base64Photo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Use JPG or PNG.'
                ], 422);
            }

            $result = $this->faceService->identifyFace(
                $base64Photo,
                $request->facegallery_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Face identification completed',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to identify face: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test compare images
     */
    public function testCompareImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_image' => 'required|string',
            'target_image' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            // Clean base64 strings
            $sourceImage = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->source_image);
            $targetImage = preg_replace('/^data:image\/[a-z]+;base64,/', '', $request->target_image);

            // Validate images
            if (!$this->faceService->validateBase64Image($sourceImage)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid source image format. Use JPG or PNG.'
                ], 422);
            }

            if (!$this->faceService->validateBase64Image($targetImage)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid target image format. Use JPG or PNG.'
                ], 422);
            }

            $result = $this->faceService->compareImages($sourceImage, $targetImage);

            return response()->json([
                'success' => true,
                'message' => 'Image comparison completed',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to compare images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List enrolled faces
     */
    public function listFaces(Request $request)
    {
        try {
            $result = $this->faceService->listFaces($request->facegallery_id);

            return response()->json([
                'success' => true,
                'message' => 'Faces listed successfully',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list faces: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete enrolled face
     */
    public function deleteFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'facegallery_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            $result = $this->faceService->deleteFace(
                $request->user_id,
                $request->facegallery_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Face deleted successfully',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete face: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get error message in Indonesian
     */
    public function getErrorMessage(Request $request)
    {
        $statusCode = $request->get('status_code', 500);
        $message = $this->faceService->getErrorMessage($statusCode);

        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message
        ]);
    }
}
