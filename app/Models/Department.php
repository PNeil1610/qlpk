<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'dept_code',
        'dept_name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================
    // CONSTANT
    // ========================

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_BUILDING = 2;

    // ========================
    // ACCESSOR
    // ========================

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Hoạt động',
            self::STATUS_INACTIVE => 'Không hoạt động',
            self::STATUS_BUILDING => 'Đang xây dựng',
            default => 'Không xác định',
        };
    }

    // ========================
    // SCOPE
    // ========================

    public function scopeActive($query)
    {
        return $query->where(
            'status',
            self::STATUS_ACTIVE
        );
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {

            $q->where('dept_code', 'like', "%$keyword%")
                ->orWhere('dept_name', 'like', "%$keyword%");

        });
    }
}