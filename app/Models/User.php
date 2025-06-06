<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'face_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the employee record associated with the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get all attendances for the user.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get today's attendances for the user.
     */
    public function todayAttendances(): HasMany
    {
        return $this->attendances()->whereDate('attendance_date', today());
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is employee.
     */
    public function isEmployee(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has face enrolled.
     */
    public function hasFaceEnrolled(): bool
    {
        return !empty($this->face_id);
    }

    /**
     * Get user's latest clock in today.
     */
    public function getTodayClockIn()
    {
        return $this->todayAttendances()
            ->where('type', 'clock_in')
            ->where('status', 'success')
            ->latest('attendance_time')
            ->first();
    }

    /**
     * Get user's latest clock out today.
     */
    public function getTodayClockOut()
    {
        return $this->todayAttendances()
            ->where('type', 'clock_out')
            ->where('status', 'success')
            ->latest('attendance_time')
            ->first();
    }

    /**
     * Check if user has clocked in today.
     */
    public function hasClockedInToday(): bool
    {
        return $this->getTodayClockIn() !== null;
    }

    /**
     * Check if user has clocked out today.
     */
    public function hasClockedOutToday(): bool
    {
        return $this->getTodayClockOut() !== null;
    }
}
