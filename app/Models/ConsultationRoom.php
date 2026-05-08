<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRoom extends Model
{
    protected $table = 'consultation_room';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'consultation_id',
        'room_id',
        'notes',
        'status',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {

            0 => 'Đã hủy',

            1 => 'Đang chờ',

            2 => 'Đang sử dụng',

            3 => 'Hoàn thành',

            default => 'Không xác định',
        };
    }
}