<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Kantor Pusat Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius' => 100,
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Bandung',
                'address' => 'Jl. Braga No. 45, Bandung, Jawa Barat',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'radius' => 150,
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Surabaya',
                'address' => 'Jl. Tunjungan No. 67, Surabaya, Jawa Timur',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'radius' => 120,
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Kantor Regional Medan',
                'address' => 'Jl. Imam Bonjol No. 89, Medan, Sumatera Utara',
                'latitude' => 3.5952,
                'longitude' => 98.6722,
                'radius' => 100,
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Warehouse Tangerang',
                'address' => 'Jl. Raya Serpong No. 234, Tangerang, Banten',
                'latitude' => -6.2293,
                'longitude' => 106.6894,
                'radius' => 200,
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
