<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescription';

    protected $fillable = [
        'consultation_id',
        'prescribed_at',
        'doctor_advice',
        'dispensed_at',
        'dispensed_by',
        'status',
    ];

    protected $casts = [
        'prescribed_at' => 'datetime',
        'dispensed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function consultation()
    {

        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }

    public function dispenser()
    {
        return $this->belongsTo(Staff::class, 'dispensed_by', 'id');
    }

    public function details()
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id', 'id');
    }
}