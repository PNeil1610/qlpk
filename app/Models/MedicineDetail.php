<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDetail extends Model
{
    use HasFactory;

    protected $table = 'medicine_detail';

    protected $fillable = [
        'medicine_id',
        'order_number',
        'unit',
        'price',
        'active_ingredient',
        'storage_condition',
        'status'
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }
}