<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in the correct order to maintain referential integrity
        $this->call([
            LocationSeeder::class,      // Create locations first
            AdminSeeder::class,         // Create users and employees (references locations)
            AttendanceSeeder::class,    // Create sample attendance data (references users)
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('=== SEEDING SUMMARY ===');
        $this->command->info('✅ Locations: Office locations and work sites');
        $this->command->info('✅ Users: Admin and sample employees');
        $this->command->info('✅ Employees: Employee profiles with location assignments');
        $this->command->info('✅ Attendance: Sample attendance records for testing');
        $this->command->info('');
        $this->command->info('🚀 Your attendance system is now ready for development and testing!');
        $this->command->info('');
        $this->command->info('Next steps:');
        $this->command->info('1. Run: php artisan storage:link');
        $this->command->info('2. Configure your .env file with database settings');
        $this->command->info('3. Set up Biznet Face API credentials in .env');
        $this->command->info('4. Start implementing the face recognition and attendance features');
    }
}
