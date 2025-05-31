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
        'status',
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
    ];

    /**
     * Get the users assigned to this location.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the employees assigned to this location.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope a query to only include active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive locations.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
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
        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            (float) $this->latitude,
            (float) $this->longitude
        );

        return $distance <= $this->radius;
    }

    /**
     * Calculate distance between two points using Haversine formula.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLngRad / 2) * sin($deltaLngRad / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get formatted coordinates.
     *
     * @return string
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        return "({$this->latitude}, {$this->longitude})";
    }

    /**
     * Get active employees count for this location.
     *
     * @return int
     */
    public function getActiveEmployeesCountAttribute(): int
    {
        return $this->users()->where('role', 'user')->where('status', 'active')->count();
    }

    /**
     * Get radius in human readable format.
     *
     * @return string
     */
    public function getFormattedRadiusAttribute(): string
    {
        if ($this->radius >= 1000) {
            return round($this->radius / 1000, 1) . ' km';
        }

        return $this->radius . ' m';
    }

    /**
     * Get all attendances recorded within this location's radius today.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayAttendances()
    {
        return Attendance::with('user')
            ->whereDate('created_at', today())
            ->get()
            ->filter(function ($attendance) {
                return $this->isWithinRadius(
                    (float) $attendance->latitude,
                    (float) $attendance->longitude
                );
            });
    }

    /**
     * Get status badge class for UI.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Find the closest location to given coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     * @return static|null
     */
    public static function findClosest(float $latitude, float $longitude): ?self
    {
        $locations = self::active()->get();

        if ($locations->isEmpty()) {
            return null;
        }

        $closest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $location->calculateDistance(
                $latitude,
                $longitude,
                (float) $location->latitude,
                (float) $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closest = $location;
            }
        }

        return $closest;
    }
}
