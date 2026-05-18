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
        'doctor_id',
        'description',
        'status',
    ];

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(PrescriptionTemplateDetail::class, 'template_id', 'id');
    }
}
