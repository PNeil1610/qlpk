<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionTemplate extends Model
{
    use HasFactory;

    protected $table = 'prescription_template';

    protected $fillable = [
        'template_name',
        'disease_name',
        'diagnosis_code',
        'doctor_id',
        'description',
        'status',
    ];

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id', 'id');
    }

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_code', 'diagnosis_code');
    }

    public function details()
    {
        return $this->hasMany(PrescriptionTemplateDetail::class, 'template_id', 'id');
    }
}
