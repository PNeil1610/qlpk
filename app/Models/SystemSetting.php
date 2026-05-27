<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    // Tên bảng theo schema mới
    protected $table = 'system_setting';

    protected $fillable = [
        'created_by',
        'clinic_name',
        'phone_number',
        'email',
        'address',
        'additional_info',
        'logo_path',
        'status',
    ];

    protected $casts = [
        'status'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Người tạo/cập nhật cấu hình
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Lấy cấu hình đang áp dụng (status=1, mới nhất)
     */
    public static function active(): ?self
    {
        return static::where('status', 1)->latest()->first();
    }
}