<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display list of employees
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'location'])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhere('employee_id', 'like', '%' . $search . '%')
                ->orWhere('position', 'like', '%' . $search . '%');
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Department filter
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        $employees = $query->paginate(20);
        $locations = Location::active()->get();
        $departments = Employee::distinct()->pluck('department')->filter();

        return view('employees.index', compact('employees', 'locations', 'departments'));
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|max:50|unique:employees',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'join_date' => 'required|date',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'is_flexible_time' => 'boolean',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user account
            $user = User::create([
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
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($employee->user_id)
            ],
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees')->ignore($employee->id)
            ],
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'location_id' => 'required|exists:locations,id',
            'join_date' => 'required|date',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'is_flexible_time' => 'boolean',
            'status' => 'required|in:active,inactive,terminated',
            'notes' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update user account
            $userData = [
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

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Employee status toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status karyawan.'
            ], 500);
        }
    }

    /**
     * Bulk actions for employees
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.'
            ], 422);
        }

        try {
            $employees = Employee::whereIn('id', $request->employee_ids)->get();
            $count = $employees->count();

            foreach ($employees as $employee) {
                switch ($request->action) {
                    case 'activate':
                        $employee->update(['status' => 'active']);
                        $employee->user->update(['is_active' => true]);
                        break;
                    case 'deactivate':
                        $employee->update(['status' => 'inactive']);
                        $employee->user->update(['is_active' => false]);
                        break;
                    case 'delete':
                        $employee->update(['status' => 'terminated']);
                        $employee->user->update(['is_active' => false]);
                        break;
                }
            }

            $actionText = [
                'activate' => 'diaktifkan',
                'deactivate' => 'dinonaktifkan',
                'delete' => 'dihapus'
            ];

            return response()->json([
                'success' => true,
                'message' => "{$count} karyawan berhasil {$actionText[$request->action]}."
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan aksi massal.'
            ], 500);
        }
    }

    /**
     * Export employees data
     */
    public function export(Request $request)
    {
        $query = Employee::with(['user', 'location']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhere('employee_id', 'like', '%' . $search . '%')
                ->orWhere('position', 'like', '%' . $search . '%');
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        $employees = $query->get();

        // Simple CSV export
        $filename = 'employees_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID Karyawan',
                'Nama',
                'Email',
                'Telepon',
                'Jabatan',
                'Departemen',
                'Lokasi',
                'Tanggal Bergabung',
                'Jam Masuk',
                'Jam Pulang',
                'Jam Fleksibel',
                'Status'
            ]);

            // Data rows
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_id,
                    $employee->user->name,
                    $employee->user->email,
                    $employee->phone,
                    $employee->position,
                    $employee->department,
                    $employee->location->name,
                    $employee->join_date->format('d/m/Y'),
                    $employee->work_start_time->format('H:i'),
                    $employee->work_end_time->format('H:i'),
                    $employee->is_flexible_time ? 'Ya' : 'Tidak',
                    ucfirst($employee->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
