<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attendanceDate = fake()->dateTimeBetween('-30 days', 'now');
        $baseTime = Carbon::parse($attendanceDate);

        // Generate realistic clock in time (7:30 AM - 9:30 AM)
        $clockInHour = fake()->numberBetween(7, 9);
        $clockInMinute = fake()->numberBetween(0, 59);
        $attendanceTime = $baseTime->copy()->setTime($clockInHour, $clockInMinute);

        // Determine if late (after 8:00 AM)
        $isLate = $attendanceTime->hour >= 8 && $attendanceTime->minute > 0;
        $lateMinutes = $isLate ? $attendanceTime->diffInMinutes($baseTime->copy()->setTime(8, 0)) : 0;

        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'type' => 'clock_in',
            'attendance_date' => $attendanceDate,
            'attendance_time' => $attendanceTime,
            'photo_path' => 'attendance/' . $attendanceDate->format('Y-m-d') . '/sample_' . fake()->uuid() . '.jpg',
            'latitude' => fake()->latitude(-6.5, -6.0), // Jakarta area
            'longitude' => fake()->longitude(106.5, 107.0),
            'is_valid_location' => fake()->boolean(95), // 95% valid location
            'distance_from_office' => fake()->numberBetween(10, 150),
            'status' => fake()->randomElement(['success', 'success', 'success', 'failed']), // 75% success
            'device_info' => fake()->userAgent(),
            'ip_address' => fake()->ipv4(),
            'face_recognition_result' => [
                'verified' => fake()->boolean(90),
                'similarity' => fake()->randomFloat(2, 0.65, 0.95),
                'masker' => fake()->boolean(20),
                'status' => 'success'
            ],
            'face_similarity_score' => fake()->randomFloat(2, 0.65, 0.95),
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'notes' => fake()->optional(0.2)->sentence(),
            'failure_reason' => null,
        ];
    }

    /**
     * Indicate that the attendance is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'is_valid_location' => true,
            'face_recognition_result' => [
                'verified' => true,
                'similarity' => fake()->randomFloat(2, 0.80, 0.95),
                'masker' => fake()->boolean(15),
                'status' => 'success'
            ],
            'face_similarity_score' => fake()->randomFloat(2, 0.80, 0.95),
            'failure_reason' => null,
        ]);
    }

    /**
     * Indicate that the attendance failed.
     */
    public function failed(): static
    {
        $failureReasons = [
            'Verifikasi wajah gagal',
            'Lokasi di luar jangkauan kantor',
            'Wajah tidak terdeteksi',
            'Kualitas foto kurang baik'
        ];

        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failure_reason' => fake()->randomElement($failureReasons),
            'face_recognition_result' => [
                'verified' => false,
                'similarity' => fake()->randomFloat(2, 0.30, 0.70),
                'masker' => fake()->boolean(30),
                'status' => 'failed'
            ],
            'face_similarity_score' => fake()->randomFloat(2, 0.30, 0.70),
        ]);
    }

    /**
     * Indicate that the attendance is clock out.
     */
    public function clockOut(): static
    {
        return $this->state(function (array $attributes) {
            $baseDate = Carbon::parse($attributes['attendance_date']);
            // Clock out time (5:00 PM - 7:00 PM)
            $clockOutHour = fake()->numberBetween(17, 19);
            $clockOutMinute = fake()->numberBetween(0, 59);
            $clockOutTime = $baseDate->copy()->setTime($clockOutHour, $clockOutMinute);

            return [
                'type' => 'clock_out',
                'attendance_time' => $clockOutTime,
                'is_late' => false,
                'late_minutes' => 0,
            ];
        });
    }

    /**
     * Indicate that the attendance is late.
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            $baseDate = Carbon::parse($attributes['attendance_date']);
            // Late clock in time (8:15 AM - 10:00 AM)
            $lateHour = fake()->numberBetween(8, 10);
            $lateMinute = fake()->numberBetween(15, 59);
            $lateTime = $baseDate->copy()->setTime($lateHour, $lateMinute);

            $scheduledTime = $baseDate->copy()->setTime(8, 0);
            $lateMinutes = $lateTime->diffInMinutes($scheduledTime);

            return [
                'attendance_time' => $lateTime,
                'is_late' => true,
                'late_minutes' => $lateMinutes,
            ];
        });
    }

    /**
     * Indicate that the attendance is on time.
     */
    public function onTime(): static
    {
        return $this->state(function (array $attributes) {
            $baseDate = Carbon::parse($attributes['attendance_date']);
            // On time clock in (7:30 AM - 8:00 AM)
            $onTimeHour = fake()->numberBetween(7, 7);
            $onTimeMinute = fake()->numberBetween(30, 59);
            $onTimeTime = $baseDate->copy()->setTime($onTimeHour, $onTimeMinute);

            return [
                'attendance_time' => $onTimeTime,
                'is_late' => false,
                'late_minutes' => 0,
            ];
        });
    }

    /**
     * Indicate that the location is invalid.
     */
    public function invalidLocation(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_valid_location' => false,
            'distance_from_office' => fake()->numberBetween(200, 1000),
            'status' => 'failed',
            'failure_reason' => 'Lokasi di luar jangkauan kantor',
        ]);
    }

    /**
     * Create attendance for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_date' => Carbon::today(),
            'attendance_time' => Carbon::today()->setTime(
                fake()->numberBetween(7, 9),
                fake()->numberBetween(0, 59)
            ),
        ]);
    }

    /**
     * Create attendance for yesterday.
     */
    public function yesterday(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_date' => Carbon::yesterday(),
            'attendance_time' => Carbon::yesterday()->setTime(
                fake()->numberBetween(7, 9),
                fake()->numberBetween(0, 59)
            ),
        ]);
    }

    /**
     * Create attendance for this week.
     */
    public function thisWeek(): static
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $randomDay = fake()->numberBetween(0, 6);

        return $this->state(fn (array $attributes) => [
            'attendance_date' => $startOfWeek->copy()->addDays($randomDay),
            'attendance_time' => $startOfWeek->copy()->addDays($randomDay)->setTime(
                fake()->numberBetween(7, 9),
                fake()->numberBetween(0, 59)
            ),
        ]);
    }

    /**
     * Create attendance with mask detection.
     */
    public function withMask(): static
    {
        return $this->state(fn (array $attributes) => [
            'face_recognition_result' => array_merge(
                $attributes['face_recognition_result'] ?? [],
                ['masker' => true]
            ),
        ]);
    }

    /**
     * Create attendance without mask.
     */
    public function withoutMask(): static
    {
        return $this->state(fn (array $attributes) => [
            'face_recognition_result' => array_merge(
                $attributes['face_recognition_result'] ?? [],
                ['masker' => false]
            ),
        ]);
    }

    /**
     * Create paired clock in and clock out for the same day.
     */
    public function paired(): static
    {
        return $this->state(function (array $attributes) {
            $baseDate = Carbon::parse($attributes['attendance_date']);

            // If this is clock_in, we'll need to create clock_out separately
            // This method is mainly for guidance, actual pairing should be done in seeder
            return $attributes;
        });
    }
}
