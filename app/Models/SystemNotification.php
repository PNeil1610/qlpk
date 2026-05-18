<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $table = 'system_notification';

    protected $fillable = [
        'sender_id',
        'target_type',
        'target_role',
        'target_user_id',
        'title',
        'content',
        'notified_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'expires_at'  => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id', 'id');
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id', 'id');
    }

    // ========================
    // 🎯 ACCESSORS
    // ========================

    /**
     * Nhãn hiển thị đối tượng nhận
     */
    public function getTargetLabelAttribute(): string
    {
        return match ($this->target_type) {
            'all'  => 'Tất cả',
            'role' => $this->getRoleLabelFromCode($this->target_role),
            'user' => $this->targetUser?->full_name ?? 'Cá nhân',
            default => '---',
        };
    }

    /**
     * Badge role của người gửi (lấy role đầu tiên)
     */
    public function getSenderRoleNameAttribute(): string
    {
        return $this->sender?->roles?->first()?->role_name ?? 'Hệ thống';
    }

    /**
     * Badge role code của người gửi (để tô màu)
     */
    public function getSenderRoleCodeAttribute(): string
    {
        return $this->sender?->roles?->first()?->role_code ?? 'SYSTEM';
    }

    // ========================
    // 🔍 SCOPES
    // ========================

    /**
     * Lọc thông báo mà $user được nhận:
     * - target_type = 'all'
     * - target_type = 'role' AND target_role = user's role_code
     * - target_type = 'user' AND target_user_id = user's id
     * + Chỉ còn hiệu lực (status=1, expires_at chưa qua)
     */
    public function scopeVisibleTo($query, User $user)
    {
        $userRoleCodes = $user->roles->pluck('role_code')->toArray();

        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) use ($user, $userRoleCodes) {
                $q->where('target_type', 'all')
                  ->orWhere(function ($q2) use ($userRoleCodes) {
                      $q2->where('target_type', 'role')
                         ->whereIn('target_role', $userRoleCodes);
                  })
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('target_type', 'user')
                         ->where('target_user_id', $user->id);
                  });
            });
    }

    /**
     * Lọc thông báo do $user tạo ra
     */
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    // ========================
    // 🧰 HELPERS
    // ========================

    public function isReadBy($userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    private function getRoleLabelFromCode(?string $code): string
    {
        return match ($code) {
            'ADMIN'        => 'Quản trị viên',
            'DOCTOR'       => 'Bác sĩ',
            'CASHIER'      => 'Thu ngân',
            'RECEPTIONIST' => 'Lễ tân',
            'NURSE'        => 'Y tá',
            default        => $code ?? '---',
        };
    }
}
