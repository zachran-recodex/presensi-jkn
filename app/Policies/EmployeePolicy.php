<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Admin can view all employees
        if ($user->isAdmin()) {
            return true;
        }

        // Employee can only view their own profile
        return $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Only admin can update employee data
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Only admin can delete (deactivate) employees
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view employee attendance history.
     */
    public function viewAttendance(User $user, Employee $employee): bool
    {
        // Admin can view all attendance
        if ($user->isAdmin()) {
            return true;
        }

        // Employee can only view their own attendance
        return $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can manage face enrollment for employee.
     */
    public function manageFaceEnrollment(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can export employee data.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can perform bulk actions on employees.
     */
    public function bulkAction(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can toggle employee status.
     */
    public function toggleStatus(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }
}
