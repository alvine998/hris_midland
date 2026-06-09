<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'status',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fcm_token' => 'encrypted',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function hasPermission(string $permission): bool
    {
        foreach ($this->userRoles as $userRole) {
            $rbac = $userRole->role?->rbac ?? [];

            if (in_array('*', $rbac) || in_array($permission, $rbac)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasPermission('*');
    }
}
