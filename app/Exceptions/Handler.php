<?php

namespace App\Exceptions;

use App\Exceptions\FaceRecognitionException;
use App\Exceptions\AttendanceException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle custom exceptions
        $this->renderable(function (FaceRecognitionException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getUserFriendlyMessage(),
                    'error_code' => $e->getErrorCode()
                ], 422);
            }

            return back()->with('error', $e->getUserFriendlyMessage());
        });

        $this->renderable(function (AttendanceException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_type' => $e->getErrorType(),
                    'data' => $e->getAdditionalData()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        });
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi login telah berakhir. Silakan login kembali.'
            ], 401);
        }

        return redirect()->guest(route('login'))
            ->with('warning', 'Silakan login untuk mengakses halaman ini.');
    }

    /**
     * Convert an authorization exception into a response.
     */
    protected function convertExceptionToResponse(Throwable $e)
    {
        if ($e instanceof AuthorizationException) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengakses resource ini.'
                ], 403);
            }
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }
        }

        return parent::convertExceptionToResponse($e);
    }

    /**
     * Convert a validation exception into a JSON response.
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Data yang dikirim tidak valid.',
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Log all errors for debugging (except validation errors)
        if (!$e instanceof ValidationException && !$e instanceof AuthenticationException) {
            Log::error('Application Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);
        }

        return parent::render($request, $e);
    }
}
