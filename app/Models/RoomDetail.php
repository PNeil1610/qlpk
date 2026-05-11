<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    protected $table = 'room_detail';

    protected $fillable = [
        'room_id',
        'floor',
        'capacity',
        'equipment_list',
        'area_sqm',
        'notes',
        'last_cleaned_at',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'floor' => 'integer',
        'capacity' => 'integer',
        'area_sqm' => 'decimal:2',
        'last_cleaned_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
