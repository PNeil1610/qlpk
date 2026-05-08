<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';

    protected $fillable = [

        'room_code',
        'room_name',
        'room_type',
        'department_id',
        'floor',
        'capacity',
        'status',

    ];

    protected $casts = [

        'department_id' => 'integer',
        'floor' => 'integer',
        'capacity' => 'integer',
        'status' => 'integer',

    ];

    // =========================
    // ROOM TYPE
    // =========================

    const TYPE_EXAMINATION = 'Examination';
    const TYPE_LAB = 'Lab';
    const TYPE_PHARMACY = 'Pharmacy';
    const TYPE_EMERGENCY = 'Emergency';
    const TYPE_WAITING = 'Waiting';
    const TYPE_ADMINISTRATIVE = 'Administrative';

    // =========================
    // STATUS
    // =========================

    const STATUS_AVAILABLE = 1;
    const STATUS_CLOSED = 0;
    const STATUS_IN_USE = 2;
    const STATUS_MAINTENANCE = 3;

    // =========================
    // RELATIONSHIP
    // =========================

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function consultations()
    {
        return $this->belongsToMany(
            Consultation::class,
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
    // =========================
    // ACCESSOR
    // =========================

    public function getStatusTextAttribute()
    {
        return match ($this->status) {

            self::STATUS_AVAILABLE => 'Hoạt động',
            self::STATUS_CLOSED => 'Đóng',
            self::STATUS_IN_USE => 'Đang sử dụng',
            self::STATUS_MAINTENANCE => 'Bảo trì',

            default => 'Không xác định',

        };
    }

    public function getRoomTypeTextAttribute()
    {
        return match ($this->room_type) {

            self::TYPE_EXAMINATION => 'Phòng khám',
            self::TYPE_LAB => 'Phòng xét nghiệm',
            self::TYPE_PHARMACY => 'Phòng phát thuốc',
            self::TYPE_EMERGENCY => 'Phòng cấp cứu',
            self::TYPE_WAITING => 'Phòng chờ',
            self::TYPE_ADMINISTRATIVE => 'Phòng hành chính',

            default => 'Không xác định',

        };
    }
}