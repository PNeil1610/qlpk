<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_type', 'staff_code', 'full_name', 'yob', 'age', 'gender',
        'email', 'phone_number', 'address', 'national_id', 'academic_degree',
        'specialty', 'medical_license', 'department_id', 'room_id',
        'position', 'user_id', 'career_start_year', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}