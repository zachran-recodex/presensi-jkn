<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Jakarta area coordinates with small variations
        $baseLat = -6.200000;
        $baseLng = 106.816666;

        // Add small random variation (within 100 meters)
        $latVariation = (fake()->numberBetween(-100, 100) / 111320); // 1 degree ≈ 111.32 km
        $lngVariation = (fake()->numberBetween(-100, 100) / (111320 * cos(deg2rad($baseLat))));

        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['in', 'out']),
            'photo_path' => 'attendance/' . fake()->uuid() . '.jpg',
            'latitude' => $baseLat + $latVariation,
            'longitude' => $baseLng + $lngVariation,
            'status' => fake()->randomElement(['success', 'failed']),
            'notes' => fake()->optional(0.3)->sentence(), // 30% chance of having notes
        ];
    }

    /**
     * Indicate that the attendance is a clock in.
     */
    public function clockIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in',
        ]);
    }

    /**
     * Indicate that the attendance is a clock out.
     */
    public function clockOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'out',
        ]);
    }

    /**
     * Indicate that the attendance was successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
        ]);
    }

    /**
     * Indicate that the attendance failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'notes' => fake()->randomElement([
                'Face recognition failed',
                'Location out of range',
                'Photo quality too low',
                'Network connection error',
            ]),
        ]);
    }

    /**
     * Set attendance time to be late (after 8:00 AM).
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            $lateTime = Carbon::today()
                ->setHour(fake()->numberBetween(8, 10))
                ->setMinute(fake()->numberBetween(1, 59))
                ->setSecond(fake()->numberBetween(0, 59));

            return [
                'type' => 'in',
                'created_at' => $lateTime,
                'updated_at' => $lateTime,
            ];
        });
    }

    /**
     * Set attendance time to be on time (before or at 8:00 AM).
     */
    public function onTime(): static
    {
        return $this->state(function (array $attributes) {
            $onTimeTime = Carbon::today()
                ->setHour(fake()->numberBetween(6, 8))
                ->setMinute(fake()->numberBetween(0, 8 === fake()->numberBetween(6, 8) ? 0 : 59))
                ->setSecond(fake()->numberBetween(0, 59));

            return [
                'type' => 'in',
                'created_at' => $onTimeTime,
                'updated_at' => $onTimeTime,
            ];
        });
    }

    /**
     * Set specific date for attendance.
     */
    public function forDate(Carbon $date): static
    {
        return $this->state(function (array $attributes) use ($date) {
            // Random time based on attendance type
            $hour = $attributes['type'] === 'in'
                ? fake()->numberBetween(7, 9)  // Clock in: 7-9 AM
                : fake()->numberBetween(16, 18); // Clock out: 4-6 PM

            $attendanceTime = $date->copy()
                ->setHour($hour)
                ->setMinute(fake()->numberBetween(0, 59))
                ->setSecond(fake()->numberBetween(0, 59));

            return [
                'created_at' => $attendanceTime,
                'updated_at' => $attendanceTime,
            ];
        });
    }

    /**
     * Set attendance for today.
     */
    public function today(): static
    {
        return $this->forDate(Carbon::today());
    }

    /**
     * Set attendance for yesterday.
     */
    public function yesterday(): static
    {
        return $this->forDate(Carbon::yesterday());
    }

    /**
     * Set custom location coordinates.
     */
    public function atLocation(float $latitude, float $longitude): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Create a pair of clock in and clock out for the same day.
     */
    public function pair(User $user, Carbon $date): array
    {
        $clockInTime = $date->copy()
            ->setHour(fake()->numberBetween(7, 9))
            ->setMinute(fake()->numberBetween(0, 59))
            ->setSecond(fake()->numberBetween(0, 59));

        $clockOutTime = $date->copy()
            ->setHour(fake()->numberBetween(16, 18))
            ->setMinute(fake()->numberBetween(0, 59))
            ->setSecond(fake()->numberBetween(0, 59));

        return [
            $this->clockIn()
                ->state([
                    'user_id' => $user->id,
                    'created_at' => $clockInTime,
                    'updated_at' => $clockInTime,
                ])
                ->create(),
            $this->clockOut()
                ->state([
                    'user_id' => $user->id,
                    'created_at' => $clockOutTime,
                    'updated_at' => $clockOutTime,
                ])
                ->create(),
        ];
    }
}
