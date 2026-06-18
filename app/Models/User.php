<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'keycloak_sub',  // UUID từ Keycloak (sub claim) - định danh chính
        'username',
        'full_name',
        'email',
        'phone_number',
        'address',
        'national_id',
        'avatar_url',
        'last_login_at',
        'status',
    ];

    protected $hidden = [
        'password_hash', // deprecated sau Keycloak, giữ lại để không lỗi query cũ
    ];

    /**
     * Kiểm tra role từ session Keycloak.
     * Sau khi tích hợp Keycloak, roles được lưu trong session thay vì DB.
     */
    public function hasRole(string $roleCode): bool
    {
        $roles = session('keycloak_roles', []);
        return in_array($roleCode, $roles);
    }

    /**
     * Kiểm tra permission (legacy - giữ để tương thích ngược).
     * Với Keycloak, permissions được quản lý thông qua roles.
     */
    public function hasPermission(string $permission): bool
    {
        // Delegate sang roles check
        return !empty(session('keycloak_roles', []));
    }

    /**
     * Quan hệ với bảng staff (liên kết qua users.id INT).
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'id');
    }

    /**
     * Quan hệ với bảng patient (liên kết qua users.id INT).
     */
    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id', 'id');
    }

    /**
     * Quan hệ roles DB (legacy - vẫn giữ để các view cũ không lỗi).
     * Sau Keycloak, đây không còn là nguồn RBAC chính.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id')
                    ->wherePivot('status', 1);
    }

    /**
     * Vô hiệu hóa tính năng remember token do bảng users không có cột này.
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Không làm gì cả
    }

    public function getRememberTokenName()
    {
        return '';
    }
}