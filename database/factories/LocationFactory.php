<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Kantor Pusat',
                'Kantor Cabang ' . fake()->city(),
                'Site Project ' . fake()->randomLetter(),
                'Gedung ' . fake()->company(),
                'Mall ' . fake()->city() . ' Branch',
                'Industrial Park ' . fake()->city(),
            ]),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-10, 5), // Indonesia latitude range
            'longitude' => fake()->longitude(95, 141), // Indonesia longitude range
            'radius' => fake()->randomElement([50, 75, 100, 150, 200, 250]),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the location is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the location is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set location as a main office.
     */
    public function mainOffice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Kantor Pusat PT. Jaka Kuasa Nusantara',
            'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius' => 100,
            'status' => 'active',
        ]);
    }

    /**
     * Set location as a branch office.
     */
    public function branchOffice(string $city = null): static
    {
        $city = $city ?? fake()->city();

        return $this->state(fn (array $attributes) => [
            'name' => "Kantor Cabang {$city}",
            'address' => fake()->streetAddress() . ", {$city}",
            'radius' => fake()->randomElement([100, 150, 200]),
            'status' => 'active',
        ]);
    }

    /**
     * Set location as a project site.
     */
    public function projectSite(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Site Project ' . fake()->randomLetter(),
            'address' => 'Kawasan Industri ' . fake()->city(),
            'radius' => fake()->randomElement([150, 200, 250, 300]),
            'status' => 'active',
        ]);
    }

    /**
     * Set custom coordinates.
     */
    public function withCoordinates(float $latitude, float $longitude): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Set custom radius.
     */
    public function withRadius(int $radius): static
    {
        return $this->state(fn (array $attributes) => [
            'radius' => $radius,
        ]);
    }

    /**
     * Create Jakarta area location.
     */
    public function jakarta(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->numberBetween(-6300000, -6100000) / 1000000, // Jakarta area
            'longitude' => fake()->numberBetween(106700000, 106900000) / 1000000, // Jakarta area
            'address' => fake()->streetAddress() . ', Jakarta',
        ]);
    }

    /**
     * Create Bandung area location.
     */
    public function bandung(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->numberBetween(-6950000, -6880000) / 1000000, // Bandung area
            'longitude' => fake()->numberBetween(107570000, 107650000) / 1000000, // Bandung area
            'address' => fake()->streetAddress() . ', Bandung',
        ]);
    }

    /**
     * Create Surabaya area location.
     */
    public function surabaya(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => fake()->numberBetween(-7300000, -7200000) / 1000000, // Surabaya area
            'longitude' => fake()->numberBetween(112700000, 112800000) / 1000000, // Surabaya area
            'address' => fake()->streetAddress() . ', Surabaya',
        ]);
    }
}
