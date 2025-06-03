<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = ['IT', 'Marketing', 'Finance', 'Human Resources', 'Sales', 'Operations', 'Creative'];
        $positions = [
            'IT' => ['Software Developer', 'System Administrator', 'DevOps Engineer', 'UI/UX Designer'],
            'Marketing' => ['Marketing Manager', 'Content Creator', 'Digital Marketing Specialist', 'Brand Manager'],
            'Finance' => ['Accountant', 'Financial Analyst', 'Budget Analyst', 'Finance Manager'],
            'Human Resources' => ['HR Specialist', 'Recruiter', 'HR Manager', 'Training Coordinator'],
            'Sales' => ['Sales Executive', 'Account Manager', 'Sales Manager', 'Business Development'],
            'Operations' => ['Operations Manager', 'Quality Assurance', 'Process Analyst', 'Logistics Coordinator'],
            'Creative' => ['Graphic Designer', 'Video Editor', 'Photographer', 'Creative Director']
        ];

        $department = fake()->randomElement($departments);
        $position = fake()->randomElement($positions[$department]);

        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'employee_id' => 'EMP' . fake()->unique()->numberBetween(1000, 9999),
            'phone' => fake()->phoneNumber(),
            'position' => $position,
            'department' => $department,
            'join_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'work_start_time' => fake()->randomElement(['08:00:00', '08:30:00', '09:00:00']),
            'work_end_time' => fake()->randomElement(['17:00:00', '17:30:00', '18:00:00']),
            'is_flexible_time' => fake()->boolean(30), // 30% chance of flexible time
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive']), // 75% active
            'notes' => fake()->optional(0.3)->sentence(),
        ];
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
     * Indicate that the employee has flexible working time.
     */
    public function flexible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_flexible_time' => true,
        ]);
    }

    /**
     * Indicate that the employee works in IT department.
     */
    public function itDepartment(): static
    {
        $positions = ['Software Developer', 'System Administrator', 'DevOps Engineer', 'UI/UX Designer'];

        return $this->state(fn (array $attributes) => [
            'department' => 'IT',
            'position' => fake()->randomElement($positions),
        ]);
    }

    /**
     * Indicate that the employee works in Marketing department.
     */
    public function marketingDepartment(): static
    {
        $positions = ['Marketing Manager', 'Content Creator', 'Digital Marketing Specialist', 'Brand Manager'];

        return $this->state(fn (array $attributes) => [
            'department' => 'Marketing',
            'position' => fake()->randomElement($positions),
        ]);
    }

    /**
     * Indicate that the employee is a recent hire.
     */
    public function recentHire(): static
    {
        return $this->state(fn (array $attributes) => [
            'join_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Indicate that the employee is a senior employee.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'join_date' => fake()->dateTimeBetween('-5 years', '-2 years'),
            'notes' => 'Karyawan senior dengan pengalaman tinggi',
        ]);
    }
}
