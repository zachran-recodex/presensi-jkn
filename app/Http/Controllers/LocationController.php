<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display list of locations
     */
    public function index()
    {
        $locations = Location::withCount('employees')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('locations.index', compact('locations'));
    }

    /**
     * Show create location form
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'timezone' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Location::create([
                'name' => $request->name,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
                'timezone' => $request->timezone,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return redirect()->route('locations.index')
                ->with('success', 'Lokasi berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Location creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan lokasi.')
                ->withInput();
        }
    }

    /**
     * Show location details
     */
    public function show(Location $location)
    {
        $location->load(['employees.user']);

        return view('locations.show', compact('location'));
    }

    /**
     * Show edit location form
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update location
     */
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'timezone' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $location->update([
                'name' => $request->name,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
                'timezone' => $request->timezone,
                'is_active' => $request->boolean('is_active')
            ]);

            return redirect()->route('locations.show', $location)
                ->with('success', 'Lokasi berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Location update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui lokasi.')
                ->withInput();
        }
    }

    /**
     * Delete location
     */
    public function destroy(Location $location)
    {
        try {
            // Check if location has employees
            if ($location->employees()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus lokasi yang masih memiliki karyawan.');
            }

            $location->delete();

            return redirect()->route('locations.index')
                ->with('success', 'Lokasi berhasil dihapus.');

        } catch (\Exception $e) {
            \Log::error('Location deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menghapus lokasi.');
        }
    }

    /**
     * Toggle location active status
     */
    public function toggleStatus(Location $location)
    {
        try {
            $location->update(['is_active' => !$location->is_active]);

            $message = $location->is_active ? 'Lokasi berhasil diaktifkan.' : 'Lokasi berhasil dinonaktifkan.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $location->is_active
            ]);

        } catch (\Exception $e) {
            \Log::error('Location status toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status lokasi.'
            ], 500);
        }
    }

    /**
     * Validate coordinates and get location info
     */
    public function validateCoordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid.'
            ], 422);
        }

        try {
            // Here you could integrate with geocoding service
            // For now, just return the coordinates
            return response()->json([
                'success' => true,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'message' => 'Koordinat valid.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi koordinat.'
            ], 500);
        }
    }

    /**
     * Get distance between two coordinates
     */
    public function calculateDistance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat1' => 'required|numeric|between:-90,90',
            'lon1' => 'required|numeric|between:-180,180',
            'lat2' => 'required|numeric|between:-90,90',
            'lon2' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid.'
            ], 422);
        }

        try {
            $distance = Location::calculateDistance(
                $request->lat1,
                $request->lon1,
                $request->lat2,
                $request->lon2
            );

            return response()->json([
                'success' => true,
                'distance' => round($distance, 2),
                'unit' => 'meters'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung jarak.'
            ], 500);
        }
    }
}
