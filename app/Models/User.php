<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'order_number',
        'username',
        'password_hash',
        'full_name',
        'email',
        'phone_number',
        'avatar_url',
        'last_login_at',
        'status'
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id')
                    ->wherePivot('status', 1);
    }

    public function hasRole($roleCode)
    {
        return $this->roles()->where('role_code', $roleCode)->exists();
    }

    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            $hasPerm = $role->permissions()
                            ->where('permission', $permission)
                            ->where('status', 1)
                            ->exists();
            if ($hasPerm) {
                return true;
            }
        }
        return false;
    }
}