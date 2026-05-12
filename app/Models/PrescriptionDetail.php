<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDetail extends Model
{
    use HasFactory;

    protected $table = 'prescription_detail';

    public $incrementing = false;
    protected $primaryKey = null;
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'quantity',
        'unit',
        'dosage',
        'usage_instructions',
        'duration_days',
        'status',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }
}