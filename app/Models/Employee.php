<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'phone',
        'position',
        'location_lat',
        'location_lng',
        'radius',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'radius' => 'integer',
    ];

    /**
     * Get the user that owns the employee record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive employees.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if employee is within allowed location radius.
     *
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function isWithinLocationRadius(float $latitude, float $longitude): bool
    {
        if (!$this->location_lat || !$this->location_lng) {
            return true; // No location restriction set
        }

        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            (float) $this->location_lat,
            (float) $this->location_lng
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
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
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
     * Get formatted location coordinates.
     *
     * @return string|null
     */
    public function getFormattedLocationAttribute(): ?string
    {
        if (!$this->location_lat || !$this->location_lng) {
            return null;
        }

        return "({$this->location_lat}, {$this->location_lng})";
    }

    /**
     * Generate unique employee ID.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateEmployeeId(string $prefix = 'EMP'): string
    {
        $year = date('Y');
        $month = date('m');

        // Get last employee ID for this month
        $lastEmployee = self::where('employee_id', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
