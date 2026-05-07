<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'roles_permission';

    protected $fillable = [
        'role_id', 
        'permission', 
        'status'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}