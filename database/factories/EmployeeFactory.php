<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_id' => $this->generateEmployeeId(),
            'phone' => fake()->phoneNumber(),
            'position' => fake()->randomElement([
                'Security Officer',
                'Security Supervisor',
                'Security Manager',
                'Site Security',
                'Building Security',
                'Guard',
            ]),
            'location_lat' => fake()->latitude(-10, 5), // Indonesia coordinates range
            'location_lng' => fake()->longitude(95, 141), // Indonesia coordinates range
            'radius' => fake()->randomElement([50, 100, 150, 200]),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Generate a realistic employee ID.
     *
     * @return string
     */
    private function generateEmployeeId(): string
    {
        $year = date('Y');
        $month = date('m');
        $random = str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return "EMP{$year}{$month}{$random}";
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the employee is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set the employee as a security officer.
     */
    public function securityOfficer(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Security Officer',
        ]);
    }

    /**
     * Set the employee as a security supervisor.
     */
    public function securitySupervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Security Supervisor',
        ]);
    }

    /**
     * Set custom location coordinates.
     */
    public function withLocation(float $latitude, float $longitude, int $radius = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'location_lat' => $latitude,
            'location_lng' => $longitude,
            'radius' => $radius,
        ]);
    }
}
