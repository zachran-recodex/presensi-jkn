<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get locations for assignment
        $locations = Location::where('is_active', true)->get();

        if ($locations->isEmpty()) {
            $this->command->error('No active locations found. Please run LocationSeeder first.');
            return;
        }

        $employees = [
            [
                'user' => [
                    'name' => 'Budi Santoso',
                    'username' => 'budi.santoso',
                    'email' => 'budi.santoso@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                'employee' => [
                    'employee_id' => 'EMP001',
                    'phone' => '081234567890',
                    'position' => 'Software Developer',
                    'department' => 'IT',
                    'join_date' => '2024-01-15',
                    'work_start_time' => '08:00',
                    'work_end_time' => '17:00',
                    'is_flexible_time' => false,
                    'status' => 'active',
                    'notes' => 'Senior developer dengan pengalaman 5 tahun'
                ]
            ],
            [
                'user' => [
                    'name' => 'Siti Nurhaliza',
                    'username' => 'siti.nurhaliza',
                    'email' => 'siti.nurhaliza@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                'employee' => [
                    'employee_id' => 'EMP002',
                    'phone' => '081234567891',
                    'position' => 'Marketing Manager',
                    'department' => 'Marketing',
                    'join_date' => '2024-02-01',
                    'work_start_time' => '08:30',
                    'work_end_time' => '17:30',
                    'is_flexible_time' => true,
                    'status' => 'active',
                    'notes' => 'Manager marketing dengan track record excellent'
                ]
            ],
            [
                'user' => [
                    'name' => 'Ahmad Fauzi',
                    'username' => 'ahmad.fauzi',
                    'email' => 'ahmad.fauzi@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                'employee' => [
                    'employee_id' => 'EMP003',
                    'phone' => '081234567892',
                    'position' => 'Accountant',
                    'department' => 'Finance',
                    'join_date' => '2024-01-03',
                    'work_start_time' => '08:00',
                    'work_end_time' => '16:00',
                    'is_flexible_time' => false,
                    'status' => 'active',
                    'notes' => 'Staff accounting berpengalaman'
                ]
            ],
            [
                'user' => [
                    'name' => 'Dewi Lestari',
                    'username' => 'dewi.lestari',
                    'email' => 'dewi.lestari@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                'employee' => [
                    'employee_id' => 'EMP004',
                    'phone' => '081234567893',
                    'position' => 'HR Specialist',
                    'department' => 'Human Resources',
                    'join_date' => '2024-03-10',
                    'work_start_time' => '08:00',
                    'work_end_time' => '17:00',
                    'is_flexible_time' => false,
                    'status' => 'active',
                    'notes' => 'Spesialis recruitment dan employee relations'
                ]
            ],
            [
                'user' => [
                    'name' => 'Rudi Hermawan',
                    'username' => 'rudi.hermawan',
                    'email' => 'rudi.hermawan@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                'employee' => [
                    'employee_id' => 'EMP005',
                    'phone' => '081234567894',
                    'position' => 'Sales Executive',
                    'department' => 'Sales',
                    'join_date' => '2024-02-20',
                    'work_start_time' => '09:00',
                    'work_end_time' => '18:00',
                    'is_flexible_time' => true,
                    'status' => 'active',
                    'notes' => 'Sales executive dengan target tinggi'
                ]
            ],
            [
                'user' => [
                    'name' => 'Maria Gonzales',
                    'username' => 'maria.gonzales',
                    'email' => 'maria.gonzales@jakakuasanusantara.web.id',
                    'password' => Hash::make('employee123'),
                    'role' => 'user',
                    'is_active' => false,
                ],
                'employee' => [
                    'employee_id' => 'EMP006',
                    'phone' => '081234567895',
                    'position' => 'Graphic Designer',
                    'department' => 'Creative',
                    'join_date' => '2023-12-01',
                    'work_start_time' => '08:30',
                    'work_end_time' => '17:30',
                    'is_flexible_time' => false,
                    'status' => 'inactive',
                    'notes' => 'Designer grafis yang sedang cuti panjang'
                ]
            ]
        ];

        foreach ($employees as $index => $employeeData) {
            // Create user
            $user = User::create($employeeData['user']);

            // Assign location (distribute employees across locations)
            $location = $locations[$index % $locations->count()];

            // Create employee profile
            Employee::create(array_merge($employeeData['employee'], [
                'user_id' => $user->id,
                'location_id' => $location->id,
            ]));
        }

        $this->command->info('Sample employees created successfully!');
        $this->command->line('');
        $this->command->line('Employee Login Credentials (password: employee123):');
        foreach ($employees as $emp) {
            $this->command->line('- ' . $emp['user']['username'] . ' (' . $emp['employee']['employee_id'] . ')');
        }
        $this->command->line('');
        $this->command->warn('Please change these passwords after first login!');
    }
}
