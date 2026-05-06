<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'patient_code',
        'full_name',
        'date_of_birth',
        'gender',
        'email',
        'phone_number',
        'address',
        'national_id',
        'blood_type',
        'insurance_number',
        'emergency_contact',
        'order_number',
        'user_id',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    // 1 patient thuộc 1 user (nếu có tài khoản)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // patient tham gia nhiều consultation (qua bảng trung gian)
    public function consultations()
    {
        return $this->belongsToMany(
            Consultation::class,
            'consultation_patient',
            'patient_id',
            'consultation_id'
        )->withPivot('notes', 'status')
         ->withTimestamps();
    }
}
