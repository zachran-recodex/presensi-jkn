<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use App\Models\Employee;
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
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@jakakuasanusantara.web.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'employee_id' => 'ADM202501001',
            'position' => 'System Administrator',
        ]);

        // Get default location (should be created by LocationSeeder first)
        $defaultLocation = Location::where('name', 'Kantor Pusat PT. Jaka Kuasa Nusantara')->first();
        $bandungLocation = Location::where('name', 'Kantor Cabang Bandung')->first();
        $surabayaLocation = Location::where('name', 'Kantor Cabang Surabaya')->first();

        // Create sample satpam users with more realistic data
        $satpamUsers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'location_id' => $defaultLocation?->id,
                'employee_id' => 'EMP202501001',
                'phone' => '08123456789',
                'position' => 'Security Officer',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'location_id' => $defaultLocation?->id,
                'employee_id' => 'EMP202501002',
                'phone' => '08123456790',
                'position' => 'Security Supervisor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'location_id' => $bandungLocation?->id,
                'employee_id' => 'EMP202501003',
                'phone' => '08123456791',
                'position' => 'Security Officer',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'location_id' => $surabayaLocation?->id,
                'employee_id' => 'EMP202501004',
                'phone' => '08123456792',
                'position' => 'Security Officer',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rina Kusuma',
                'email' => 'rina@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'location_id' => $defaultLocation?->id,
                'employee_id' => 'EMP202501005',
                'phone' => '08123456793',
                'position' => 'Security Officer',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dani Prasetyo',
                'email' => 'dani@jakakuasanusantara.web.id',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'inactive', // Example of inactive user
                'location_id' => $defaultLocation?->id,
                'employee_id' => 'EMP202501006',
                'phone' => '08123456794',
                'position' => 'Security Officer',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($satpamUsers as $userData) {
            $user = User::create($userData);

            // Create corresponding employee record
            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $user->employee_id,
                'phone' => $user->phone,
                'position' => $user->position,
                'location_lat' => $user->location?->latitude,
                'location_lng' => $user->location?->longitude,
                'radius' => $user->location?->radius ?? 100,
                'status' => $user->status,
            ]);
        }

        $this->command->info('Admin and sample users created successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Admin Account:');
        $this->command->info('Email: admin@jakakuasanusantara.web.id');
        $this->command->info('Password: admin123');
        $this->command->info('');
        $this->command->info('Sample User Accounts:');
        foreach ($satpamUsers as $user) {
            $this->command->info("Email: {$user['email']} | Password: password123 | Status: {$user['status']}");
        }
        $this->command->info('');
        $this->command->info('Total Users Created: ' . (count($satpamUsers) + 1));
        $this->command->info('Active Users: ' . count(array_filter($satpamUsers, fn($u) => $u['status'] === 'active')));
        $this->command->info('Inactive Users: ' . count(array_filter($satpamUsers, fn($u) => $u['status'] === 'inactive')));
    }
}
