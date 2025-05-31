<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
            'face_id' => null,
            'is_face_enrolled' => false,
            'status' => 'active',
            'location_id' => null,
            'employee_id' => $this->generateEmployeeId(),
            'phone' => fake()->phoneNumber(),
            'position' => fake()->randomElement([
                'Security Officer',
                'Security Supervisor',
                'Security Manager',
                'Site Security',
                'Building Security',
                'Guard',
                'Security Coordinator',
            ]),
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
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'employee_id' => 'ADM' . date('Ym') . str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'position' => fake()->randomElement([
                'System Administrator',
                'HR Manager',
                'Operations Manager',
                'General Manager',
            ]),
        ]);
    }

    /**
     * Indicate that the user is a regular user (security staff).
     */
    public function regularUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }

    /**
     * Indicate that the user is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the user has face enrolled.
     */
    public function withFaceEnrolled(): static
    {
        return $this->state(fn (array $attributes) => [
            'face_id' => 'face_' . Str::random(16),
            'is_face_enrolled' => true,
        ]);
    }

    /**
     * Indicate that the user doesn't have face enrolled.
     */
    public function withoutFaceEnrolled(): static
    {
        return $this->state(fn (array $attributes) => [
            'face_id' => null,
            'is_face_enrolled' => false,
        ]);
    }

    /**
     * Assign the user to a specific location.
     */
    public function atLocation(Location $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location_id' => $location->id,
        ]);
    }

    /**
     * Set custom employee ID.
     */
    public function withEmployeeId(string $employeeId): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employeeId,
        ]);
    }

    /**
     * Set user as security officer.
     */
    public function securityOfficer(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Security Officer',
            'role' => 'user',
        ]);
    }

    /**
     * Set user as security supervisor.
     */
    public function securitySupervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Security Supervisor',
            'role' => 'user',
        ]);
    }

    /**
     * Set user as security manager.
     */
    public function securityManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Security Manager',
            'role' => 'user',
        ]);
    }

    /**
     * Create user with specific credentials for testing.
     */
    public function withCredentials(string $email, string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
