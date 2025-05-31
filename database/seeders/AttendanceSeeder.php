<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active users (excluding admin)
        $users = User::where('role', 'user')
            ->where('status', 'active')
            ->with('location')
            ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No active users found. Please run AdminSeeder first.');
            return;
        }

        // Create attendance records for the last 10 days
        $attendanceCount = 0;

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Skip weekends for more realistic data
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($users as $user) {
                // 90% chance of attendance on any given day
                if (rand(1, 100) <= 90) {
                    $this->createAttendanceForUser($user, $date);
                    $attendanceCount += 2; // Clock in + Clock out
                }
            }
        }

        $this->command->info("Sample attendance records created successfully!");
        $this->command->info("Total attendance records: {$attendanceCount}");
        $this->command->info("Coverage: Last 10 working days");
    }

    /**
     * Create clock in and clock out records for a user on a specific date.
     *
     * @param User $user
     * @param Carbon $date
     */
    private function createAttendanceForUser(User $user, Carbon $date): void
    {
        $location = $user->location;

        // Use user's assigned location or default coordinates
        $latitude = $location ? (float) $location->latitude : -6.200000;
        $longitude = $location ? (float) $location->longitude : 106.816666;

        // Add small random variation to coordinates (within 50 meters)
        $latVariation = (rand(-50, 50) / 111320); // 1 degree ≈ 111.32 km
        $lngVariation = (rand(-50, 50) / (111320 * cos(deg2rad($latitude))));

        $clockInLat = $latitude + $latVariation;
        $clockInLng = $longitude + $lngVariation;

        // Clock In Time (7:30 AM - 9:00 AM)
        $clockInHour = rand(7, 8);
        $clockInMinute = rand(0, 59);

        // Make some late arrivals (after 8:00 AM)
        if (rand(1, 100) <= 20) { // 20% chance of being late
            $clockInHour = rand(8, 9);
            if ($clockInHour === 8) {
                $clockInMinute = rand(1, 59); // Late if after 8:00
            }
        }

        $clockInTime = $date->copy()
            ->setHour($clockInHour)
            ->setMinute($clockInMinute)
            ->setSecond(rand(0, 59));

        // Clock Out Time (4:00 PM - 6:00 PM)
        $clockOutHour = rand(16, 18);
        $clockOutMinute = rand(0, 59);

        $clockOutTime = $date->copy()
            ->setHour($clockOutHour)
            ->setMinute($clockOutMinute)
            ->setSecond(rand(0, 59));

        // Create dummy photo paths
        $clockInPhoto = 'attendance/dummy_' . $user->id . '_' . $date->format('Ymd') . '_in.jpg';
        $clockOutPhoto = 'attendance/dummy_' . $user->id . '_' . $date->format('Ymd') . '_out.jpg';

        // Clock In Record
        Attendance::create([
            'user_id' => $user->id,
            'type' => 'in',
            'photo_path' => $clockInPhoto,
            'latitude' => $clockInLat,
            'longitude' => $clockInLng,
            'status' => 'success',
            'notes' => 'Seeded attendance data',
            'created_at' => $clockInTime,
            'updated_at' => $clockInTime,
        ]);

        // Clock Out Record (only if not today or if today and it's already past work hours)
        $now = Carbon::now();
        if ($date->toDateString() !== $now->toDateString() || $now->hour >= 16) {
            // Small variation in coordinates for clock out
            $clockOutLatVariation = (rand(-20, 20) / 111320);
            $clockOutLngVariation = (rand(-20, 20) / (111320 * cos(deg2rad($latitude))));

            Attendance::create([
                'user_id' => $user->id,
                'type' => 'out',
                'photo_path' => $clockOutPhoto,
                'latitude' => $clockInLat + $clockOutLatVariation,
                'longitude' => $clockInLng + $clockOutLngVariation,
                'status' => 'success',
                'notes' => 'Seeded attendance data',
                'created_at' => $clockOutTime,
                'updated_at' => $clockOutTime,
            ]);
        }
    }

    /**
     * Create dummy photo files (optional - for testing purposes).
     */
    private function createDummyPhotoFiles(): void
    {
        // This method can be used to create actual dummy image files
        // if needed for testing photo display functionality

        if (!Storage::disk('public')->exists('attendance')) {
            Storage::disk('public')->makeDirectory('attendance');
        }

        // You can add logic here to create actual dummy image files
        // For now, we just ensure the directory exists

        $this->command->info('Attendance photo directory created.');
    }
}
