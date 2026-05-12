<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicine';

    protected $fillable = [
        'medicine_category_id',
        'medicine_code',
        'medicine_name',
        'note',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(MedicineCategory::class, 'medicine_category_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(MedicineDetail::class, 'medicine_id', 'id');
    }
}