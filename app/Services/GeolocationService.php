<?php

namespace App\Services;

use App\Models\Location;

class GeolocationService
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 Latitude 1
     * @param float $lon1 Longitude 1
     * @param float $lat2 Latitude 2
     * @param float $lon2 Longitude 2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return Location::calculateDistance($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Validate if coordinates are within allowed location radius
     *
     * @param float $userLat User's latitude
     * @param float $userLng User's longitude
     * @param Location $allowedLocation Allowed office location
     * @return array
     */
    public function validateLocation(float $userLat, float $userLng, Location $allowedLocation): array
    {
        $distance = $this->calculateDistance(
            $userLat,
            $userLng,
            $allowedLocation->latitude,
            $allowedLocation->longitude
        );

        $isValid = $distance <= $allowedLocation->radius;

        return [
            'is_valid' => $isValid,
            'distance' => round($distance, 2),
            'allowed_radius' => $allowedLocation->radius,
            'location_name' => $allowedLocation->name,
            'coordinates' => [
                'user' => ['lat' => $userLat, 'lng' => $userLng],
                'office' => ['lat' => $allowedLocation->latitude, 'lng' => $allowedLocation->longitude]
            ]
        ];
    }

    /**
     * Find the nearest office location from user coordinates
     *
     * @param float $userLat
     * @param float $userLng
     * @return array
     */
    public function findNearestLocation(float $userLat, float $userLng): array
    {
        $activeLocations = Location::where('is_active', true)->get();

        if ($activeLocations->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada lokasi kantor yang aktif'
            ];
        }

        $nearestLocation = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($activeLocations as $location) {
            $distance = $this->calculateDistance(
                $userLat,
                $userLng,
                $location->latitude,
                $location->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestLocation = $location;
            }
        }

        return [
            'success' => true,
            'location' => $nearestLocation,
            'distance' => round($shortestDistance, 2),
            'is_within_radius' => $shortestDistance <= $nearestLocation->radius
        ];
    }

    /**
     * Get all locations within certain radius from user
     *
     * @param float $userLat
     * @param float $userLng
     * @param float $radiusKm Radius in kilometers
     * @return array
     */
    public function getLocationsWithinRadius(float $userLat, float $userLng, float $radiusKm = 10): array
    {
        $activeLocations = Location::where('is_active', true)->get();
        $radiusMeters = $radiusKm * 1000;

        $nearbyLocations = [];

        foreach ($activeLocations as $location) {
            $distance = $this->calculateDistance(
                $userLat,
                $userLng,
                $location->latitude,
                $location->longitude
            );

            if ($distance <= $radiusMeters) {
                $nearbyLocations[] = [
                    'location' => $location,
                    'distance' => round($distance, 2),
                    'is_within_attendance_radius' => $distance <= $location->radius
                ];
            }
        }

        // Sort by distance
        usort($nearbyLocations, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $nearbyLocations;
    }

    /**
     * Validate coordinate format
     *
     * @param mixed $latitude
     * @param mixed $longitude
     * @return array
     */
    public function validateCoordinates($latitude, $longitude): array
    {
        $errors = [];

        // Check if coordinates are numeric
        if (!is_numeric($latitude)) {
            $errors[] = 'Latitude harus berupa angka';
        }

        if (!is_numeric($longitude)) {
            $errors[] = 'Longitude harus berupa angka';
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        $lat = (float) $latitude;
        $lng = (float) $longitude;

        // Check coordinate ranges
        if ($lat < -90 || $lat > 90) {
            $errors[] = 'Latitude harus antara -90 sampai 90';
        }

        if ($lng < -180 || $lng > 180) {
            $errors[] = 'Longitude harus antara -180 sampai 180';
        }

        // Check if coordinates are likely in Indonesia
        if ($lat < -11 || $lat > 6 || $lng < 95 || $lng > 141) {
            $errors[] = 'Koordinat di luar wilayah Indonesia';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'coordinates' => [
                'latitude' => $lat,
                'longitude' => $lng
            ]
        ];
    }

    /**
     * Get address from coordinates (reverse geocoding)
     * Note: This is a placeholder - implement with actual geocoding service
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getAddressFromCoordinates(float $latitude, float $longitude): array
    {
        // Placeholder implementation
        // In real implementation, integrate with Google Maps Geocoding API or similar service

        return [
            'success' => false,
            'message' => 'Geocoding service not implemented',
            'address' => "Koordinat: {$latitude}, {$longitude}"
        ];
    }

    /**
     * Get travel time estimation between two points
     * Note: This is a placeholder - implement with actual routing service
     *
     * @param float $fromLat
     * @param float $fromLng
     * @param float $toLat
     * @param float $toLng
     * @return array
     */
    public function getTravelTime(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $distance = $this->calculateDistance($fromLat, $fromLng, $toLat, $toLng);

        // Simple estimation: 50 km/h average speed
        $estimatedTimeMinutes = ($distance / 1000) * (60 / 50);

        return [
            'distance_meters' => round($distance, 2),
            'estimated_time_minutes' => round($estimatedTimeMinutes, 0),
            'method' => 'straight_line_estimation'
        ];
    }

    /**
     * Check if location coordinates are valid for office use
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function validateOfficeLocation(float $latitude, float $longitude): array
    {
        $coordValidation = $this->validateCoordinates($latitude, $longitude);

        if (!$coordValidation['valid']) {
            return $coordValidation;
        }

        // Additional checks for office locations
        $warnings = [];

        // Check if location is in major Indonesian cities (rough bounds)
        $majorCities = [
            'Jakarta' => ['lat_min' => -6.4, 'lat_max' => -6.0, 'lng_min' => 106.6, 'lng_max' => 107.0],
            'Bandung' => ['lat_min' => -7.0, 'lat_max' => -6.8, 'lng_min' => 107.5, 'lng_max' => 107.7],
            'Surabaya' => ['lat_min' => -7.4, 'lat_max' => -7.1, 'lng_min' => 112.6, 'lng_max' => 112.9],
            'Medan' => ['lat_min' => 3.4, 'lat_max' => 3.7, 'lng_min' => 98.5, 'lng_max' => 98.8],
        ];

        $inMajorCity = false;
        foreach ($majorCities as $city => $bounds) {
            if ($latitude >= $bounds['lat_min'] && $latitude <= $bounds['lat_max'] &&
                $longitude >= $bounds['lng_min'] && $longitude <= $bounds['lng_max']) {
                $inMajorCity = $city;
                break;
            }
        }

        if (!$inMajorCity) {
            $warnings[] = 'Lokasi di luar kota besar utama Indonesia';
        }

        return [
            'valid' => true,
            'warnings' => $warnings,
            'city' => $inMajorCity ?: 'Unknown',
            'coordinates' => [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]
        ];
    }
}
