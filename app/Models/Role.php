<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'role_code', 
        'role_name', 
        'description', 
        'status'
    ];

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }
}