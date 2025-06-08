<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display list of employees with sorting
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'location']);

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort fields
        $allowedSortFields = [
            'created_at', 'employee_id', 'position', 'department',
            'join_date', 'status', 'user.name', 'location.name'
        ];

        if (in_array($sortField, $allowedSortFields)) {
            if (str_contains($sortField, '.')) {
                // Handle relationship sorting
                $parts = explode('.', $sortField);
                $relation = $parts[0];
                $field = $parts[1];
                $query->join($relation === 'user' ? 'users' : 'locations',
                    $relation === 'user' ? 'employees.user_id' : 'employees.location_id',
                    '=',
                    $relation === 'user' ? 'users.id' : 'locations.id')
                    ->orderBy($relation === 'user' ? "users.{$field}" : "locations.{$field}", $sortDirection)
                    ->select('employees.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $employees = $query->paginate(20);
        $locations = Location::active()->get();

        return view('employees.index', compact('employees', 'locations', 'sortField', 'sortDirection'));
    }

    /**
     * Show create employee form
     */
    public function create()
    {
        $locations = Location::active()->get();
        return view('employees.create', compact('locations'));
    }

    /**
     * Store new employee
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            // Create user account
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'is_active' => $request->status === 'active'
            ]);

            // Create employee profile
            Employee::create([
                'user_id' => $user->id,
                'location_id' => $request->location_id,
                'employee_id' => $request->employee_id,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'join_date' => $request->join_date,
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
                'is_flexible_time' => $request->boolean('is_flexible_time'),
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return redirect()->route('employees.index')
                ->with('success', 'Karyawan berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Employee creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan karyawan.')
                ->withInput();
        }
    }

    /**
     * Show employee details
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'location', 'attendances' => function($query) {
            $query->orderBy('attendance_date', 'desc')->limit(10);
        }]);

        // Monthly attendance summary
        $monthlySummary = $employee->getMonthlyAttendanceSummary(
            now()->year,
            now()->month
        );

        return view('employees.show', compact('employee', 'monthlySummary'));
    }

    /**
     * Show edit employee form
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');
        $locations = Location::active()->get();
        return view('employees.edit', compact('employee', 'locations'));
    }

    /**
     * Update employee
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            // Update user account
            $userData = [
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->status === 'active'
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);

            // Update employee profile
            $employee->update([
                'location_id' => $request->location_id,
                'employee_id' => $request->employee_id,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'join_date' => $request->join_date,
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
                'is_flexible_time' => $request->boolean('is_flexible_time'),
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return redirect()->route('employees.show', $employee)
                ->with('success', 'Data karyawan berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Employee update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data karyawan.')
                ->withInput();
        }
    }

    /**
     * Delete employee (soft delete by changing status)
     */
    public function destroy(Employee $employee)
    {
        try {
            // Don't actually delete, just change status
            $employee->update(['status' => 'terminated']);
            $employee->user->update(['is_active' => false]);

            return redirect()->route('employees.index')
                ->with('success', 'Karyawan berhasil dinonaktifkan.');

        } catch (\Exception $e) {
            Log::error('Employee deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan karyawan.');
        }
    }

    /**
     * Activate/deactivate employee
     */
    public function toggleStatus(Employee $employee)
    {
        try {
            $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
            $isActive = $newStatus === 'active';

            $employee->update(['status' => $newStatus]);
            $employee->user->update(['is_active' => $isActive]);

            $message = $isActive ? 'Karyawan berhasil diaktifkan.' : 'Karyawan berhasil dinonaktifkan.';

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Employee status toggle error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengubah status karyawan.');
        }
    }
}
