<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has employee profile
        if (!$user->employee) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil karyawan tidak ditemukan. Hubungi admin.'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Profil karyawan tidak ditemukan. Hubungi admin.');
        }

        // Check if employee is active
        if ($user->employee->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun karyawan tidak aktif. Hubungi admin.'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Akun karyawan tidak aktif. Hubungi admin.');
        }

        // Check if user account is active
        if (!$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun pengguna tidak aktif. Hubungi admin.'
                ], 403);
            }

            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin.');
        }

        return $next($request);
    }
}
