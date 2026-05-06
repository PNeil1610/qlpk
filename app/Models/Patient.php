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
        'gender',
        'email',
        'phone_number',
        'address',
        'national_id',
        'insurance_number',
        'emergency_contact',
        'age',          // 
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================
    // 🔥 CONSTANT (ENUM)
    // ========================

    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';
    const GENDER_OTHER = 'Other';

    const STATUS_ACTIVE = 1;        // Hoạt động
    const STATUS_INACTIVE = 0;        // Không hoạt động

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    public function consultations()
{
    return $this->hasMany(Consultation::class, 'patient_id');
}

    // ========================
    // 🎯 ACCESSOR
    // ========================

    public function getGenderTextAttribute()
    {
        return match ($this->gender) {
            self::GENDER_MALE => 'Nam',
            self::GENDER_FEMALE => 'Nữ',
            default => 'Khác',
        };
    }

   public function getStatusTextAttribute()
{
    return match ($this->status) {
        self::STATUS_ACTIVE => 'Hoạt động',
        self::STATUS_INACTIVE => 'Không hoạt động',
        default => 'Không xác định',
    };
}

    // ========================
    // 🔍 SCOPE
    // ========================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSearch($query, $keyword)
{
    return $query->where(function ($q) use ($keyword) {
        $q->where('full_name', 'like', "%$keyword%")
          ->orWhere('phone_number', 'like', "%$keyword%")
          ->orWhere('patient_code', 'like', "%$keyword%");
    });
}
}