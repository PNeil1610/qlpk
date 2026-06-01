<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultation';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // ========================
    // 🧾 FILLABLE
    // ========================
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'order_number',
        'consultation_date',
        'symptoms',
        'temperature',
        'blood_pressure',
        'diagnosis_code',
        'diagnosis_notes',
        'chief_complaint',
        'follow_up_date',
        'status',
    ];

    // ========================
    // 🔄 CASTS
    // ========================
    protected $casts = [
        'consultation_date' => 'datetime',
        'follow_up_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'temperature' => 'decimal:1',
        'status' => 'integer',
    ];

    // ========================
    // 🔥 CONSTANT (STATUS)
    // ========================
    const STATUS_NEW = 0;
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_WAIT_PAYMENT = 3;
    const STATUS_COMPLETED = 4;

    // ========================
    // 🎯 ACCESSOR
    // ========================
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'Mới tạo',
            self::STATUS_PENDING => 'Chờ',
            self::STATUS_PROCESSING => 'Đang khám',
            self::STATUS_WAIT_PAYMENT => 'Chờ thanh toán',
            self::STATUS_COMPLETED => 'Hoàn thành',
            default => 'Không xác định',
        };
    }

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    // 🔥 1 consultation thuộc 1 patient
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    // 🔥 1 consultation thuộc 1 doctor (Staff)
    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    // 🔥 1 consultation có 1diagnosis
    public function diagnosis()
    {
        return $this->belongsTo(
            Diagnosis::class,
            'diagnosis_code',
            'diagnosis_code'
        );
    }

    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'consultation_room'
        )
            ->withPivot([
                'notes',
                'status',
                'created_at',
                'updated_at'
            ]);
    }

    public function consultationRooms()
    {
        return $this->hasMany(ConsultationRoom::class);
    }

    // ========================
    // 🔍 SCOPE
    // ========================

    public function scopeToday($query)
    {
        return $query->whereDate('consultation_date', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function prescriptions()
    {
        return $this->hasMany(\App\Models\Prescription::class, 'consultation_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class, 'consultation_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'consultation_id', 'id');
    }
}