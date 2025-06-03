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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = [
            'Jakarta' => [
                'names' => ['Kantor Pusat Jakarta', 'Cabang Jakarta Selatan', 'Cabang Jakarta Utara'],
                'lat_range' => [-6.3, -6.1],
                'lng_range' => [106.7, 106.9]
            ],
            'Bandung' => [
                'names' => ['Cabang Bandung', 'Kantor Regional Bandung'],
                'lat_range' => [-6.95, -6.85],
                'lng_range' => [107.55, 107.65]
            ],
            'Surabaya' => [
                'names' => ['Cabang Surabaya', 'Kantor Regional Surabaya'],
                'lat_range' => [-7.3, -7.2],
                'lng_range' => [112.7, 112.8]
            ],
            'Medan' => [
                'names' => ['Kantor Regional Medan', 'Cabang Medan'],
                'lat_range' => [3.55, 3.65],
                'lng_range' => [98.6, 98.7]
            ],
            'Semarang' => [
                'names' => ['Cabang Semarang'],
                'lat_range' => [-7.0, -6.9],
                'lng_range' => [110.3, 110.5]
            ]
        ];

        $city = fake()->randomElement(array_keys($cities));
        $cityData = $cities[$city];

        return [
            'name' => fake()->randomElement($cityData['names']),
            'address' => fake()->streetAddress() . ', ' . $city,
            'latitude' => fake()->randomFloat(8, $cityData['lat_range'][0], $cityData['lat_range'][1]),
            'longitude' => fake()->randomFloat(8, $cityData['lng_range'][0], $cityData['lng_range'][1]),
            'radius' => fake()->numberBetween(50, 200),
            'timezone' => 'Asia/Jakarta',
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the location is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the location is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a main office location.
     */
    public function mainOffice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
            'is_active' => true,
        ]);
    }

    /**
     * Create a branch office location.
     */
    public function branch(string $city = null): static
    {
        $cityName = $city ?: fake()->randomElement(['Bandung', 'Surabaya', 'Medan', 'Semarang']);

        return $this->state(fn (array $attributes) => [
            'name' => "Cabang {$cityName}",
            'address' => fake()->streetAddress() . ", {$cityName}",
            'radius' => fake()->numberBetween(80, 150),
        ]);
    }

    /**
     * Create a warehouse location.
     */
    public function warehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Warehouse ' . fake()->city(),
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'radius' => fake()->numberBetween(150, 300), // Larger radius for warehouse
        ]);
    }

    /**
     * Create location with specific coordinates.
     */
    public function withCoordinates(float $latitude, float $longitude): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Create location with large radius.
     */
    public function largeRadius(): static
    {
        return $this->state(fn (array $attributes) => [
            'radius' => fake()->numberBetween(200, 500),
        ]);
    }

    /**
     * Create location with small radius.
     */
    public function smallRadius(): static
    {
        return $this->state(fn (array $attributes) => [
            'radius' => fake()->numberBetween(30, 80),
        ]);
    }
}
