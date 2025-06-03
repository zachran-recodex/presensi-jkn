<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@jakakuasanusantara.web.id',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create backup admin user
        User::create([
            'name' => 'Admin Backup',
            'email' => 'backup@jakakuasanusantara.web.id',
            'email_verified_at' => now(),
            'password' => Hash::make('backup123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create HR admin user
        User::create([
            'name' => 'HR Manager',
            'email' => 'hr@jakakuasanusantara.web.id',
            'email_verified_at' => now(),
            'password' => Hash::make('hr123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('Admin users created successfully!');
        $this->command->line('');
        $this->command->line('Admin Login Credentials:');
        $this->command->line('1. admin@jakakuasanusantara.web.id / admin123456');
        $this->command->line('2. backup@jakakuasanusantara.web.id / backup123456');
        $this->command->line('3. hr@jakakuasanusantara.web.id / hr123456');
        $this->command->line('');
        $this->command->warn('Please change these passwords after first login!');
    }
}
