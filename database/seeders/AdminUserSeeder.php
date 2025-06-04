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
            'username' => 'super-admin',
            'name' => 'Administrator',
            'email' => 'admin@jakakuasanusantara.web.id',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create backup admin user
        User::create([
            'username' => 'admin',
            'name' => 'Admin Backup',
            'email' => 'backup@jakakuasanusantara.web.id',
            'password' => Hash::make('backup123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create HR admin user
        User::create([
            'username' => 'hr-manager',
            'name' => 'HR Manager',
            'email' => 'hr@jakakuasanusantara.web.id',
            'password' => Hash::make('hr123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'zachranraze',
            'name' => 'Zachran Razendra',
            'email' => 'zachranraaze@jakakuasanusantara.web.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'sinyo',
            'name' => 'Sinyo Simpers',
            'email' => 'sinyo@jakakuasanusantara.web.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('Admin users created successfully!');
        $this->command->line('');
        $this->command->line('Admin Login Credentials:');
        $this->command->line('1. super-admin / admin123456');
        $this->command->line('2. admin / backup123456');
        $this->command->line('3. hr-manager / hr123456');
        $this->command->line('');
        $this->command->warn('Please change these passwords after first login!');
    }
}
