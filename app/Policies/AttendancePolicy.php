<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all attendance, employees can view their own
        return $user->isAdmin() || ($user->employee && $user->employee->status === 'active');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        // Admin can view all attendance
        if ($user->isAdmin()) {
            return true;
        }

        // Employee can only view their own attendance
        return $user->id === $attendance->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only active employees can create attendance
        if (!$user->employee) {
            return false;
        }

        return $user->employee->status === 'active' && $user->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        // Generally, attendance records should not be updated after creation
        // Only admin can update in special cases (like fixing wrong records)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        // Only admin can delete attendance records
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can perform clock in.
     */
    public function clockIn(User $user): bool
    {
        // Check if user is active employee
        if (!$user->employee || $user->employee->status !== 'active' || !$user->is_active) {
            return false;
        }

        // Check if user has face enrolled
        if (!$user->hasFaceEnrolled()) {
            return false;
        }

        // Check if user has already clocked in today
        return !$user->hasClockedInToday();
    }

    /**
     * Determine whether the user can perform clock out.
     */
    public function clockOut(User $user): bool
    {
        // Check if user is active employee
        if (!$user->employee || $user->employee->status !== 'active' || !$user->is_active) {
            return false;
        }

        // Check if user has face enrolled
        if (!$user->hasFaceEnrolled()) {
            return false;
        }

        // Check if user has clocked in but not clocked out today
        return $user->hasClockedInToday() && !$user->hasClockedOutToday();
    }

    /**
     * Determine whether the user can view attendance reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can export attendance data.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view admin attendance history.
     */
    public function viewAdminHistory(User $user): bool
    {
        return $user->isAdmin();
    }
}
