<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionTemplateDetail extends Model
{
    use HasFactory;

    protected $table = 'prescription_template_detail';
    
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'template_id',
        'medicine_id',
        'quantity',
        'unit',
        'dosage',
        'usage_instructions',
        'duration_days',
        'status',
    ];

    public function template()
    {
        return $this->belongsTo(PrescriptionTemplate::class, 'template_id', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }
}
