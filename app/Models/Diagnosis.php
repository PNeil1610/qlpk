<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Diagnosis extends Model
{
    use HasFactory;

    protected $table = 'diagnosis';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'diagnosis_code',
        'diagnosis_name',
        'description',
        'icd_version',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================
    // STATUS
    // =========================

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // =========================
    // ACCESSOR
    // =========================

    public function getStatusTextAttribute()
    {
        return $this->status == self::STATUS_ACTIVE
            ? 'Hoạt động'
            : 'Ngưng';
    }

    // =========================
    // RELATIONSHIP
    // =========================

    public function consultations()
    {
        return $this->hasMany(
            Consultation::class,
            'diagnosis_code',
            'diagnosis_code'
        );
    }

    // =========================
    // SCOPE
    // =========================

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('diagnosis_code', 'like', "%$keyword%")
            ->orWhere('diagnosis_name', 'like', "%$keyword%");
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $diagnoses = Diagnosis::where('diagnosis_name', 'like', "%$keyword%")
            ->orWhere('diagnosis_code', 'like', "%$keyword%")
            ->latest()
            ->get();

        return response()->json($diagnoses);
    }

}