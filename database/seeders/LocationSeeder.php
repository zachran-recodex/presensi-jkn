<?php

namespace Database\Seeders;

use App\Models\Location;
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
                'name' => 'Kantor Pusat PT. Jaka Kuasa Nusantara',
                'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius' => 100,
                'status' => 'active',
            ],
            [
                'name' => 'Kantor Cabang Bandung',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'radius' => 150,
                'status' => 'active',
            ],
            [
                'name' => 'Kantor Cabang Surabaya',
                'address' => 'Jl. Pemuda No. 78, Surabaya',
                'latitude' => -7.257472,
                'longitude' => 112.752090,
                'radius' => 120,
                'status' => 'active',
            ],
            [
                'name' => 'Site Project A',
                'address' => 'Kawasan Industri MM2100, Bekasi',
                'latitude' => -6.270000,
                'longitude' => 107.150000,
                'radius' => 200,
                'status' => 'active',
            ],
            [
                'name' => 'Site Project B',
                'address' => 'Kawasan PIK, Jakarta Utara',
                'latitude' => -6.107000,
                'longitude' => 106.740000,
                'radius' => 150,
                'status' => 'inactive', // Example of inactive location
            ],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        $this->command->info('Locations seeded successfully!');
        $this->command->info('Created ' . count($locations) . ' locations');
    }
}
