<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@jakakuasanusantara.web.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample satpam users
        $satpamUsers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
            ],
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
            ],
        ];

        foreach ($satpamUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('Admin and sample users created successfully!');
        $this->command->info('Admin credentials:');
        $this->command->info('Email: admin@jakakuasanusantara.web.id');
        $this->command->info('Password: admin123');
    }
}
