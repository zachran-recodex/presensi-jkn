<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius',
        'timezone',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all employees assigned to this location.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get all attendances recorded at this location.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     *
     * @param float $lat1 Latitude 1
     * @param float $lon1 Longitude 1
     * @param float $lat2 Latitude 2
     * @param float $lon2 Longitude 2
     * @return float Distance in meters
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if given coordinates are within this location's radius.
     *
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function isWithinRadius(float $latitude, float $longitude): bool
    {
        $distance = self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );

        return $distance <= $this->radius;
    }

    /**
     * Get distance from given coordinates to this location.
     *
     * @param float $latitude
     * @param float $longitude
     * @return float Distance in meters
     */
    public function getDistanceFrom(float $latitude, float $longitude): float
    {
        return self::calculateDistance(
            $this->latitude,
            $this->longitude,
            $latitude,
            $longitude
        );
    }

    /**
     * Scope to get only active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
