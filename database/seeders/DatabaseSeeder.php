<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            AdminUserSeeder::class,
            SampleEmployeeSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('');
        $this->command->info('Next steps:');
        $this->command->info('1. Run: php artisan migrate --seed');
        $this->command->info('2. Setup Biznet Face API credentials in .env');
        $this->command->info('3. Test login with admin credentials');
        $this->command->info('4. Enroll employee faces through admin panel');
        $this->command->info('');
    }
}
