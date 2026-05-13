<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // ========================
    // 🧾 FILLABLE
    // ========================
    protected $fillable = [
        'invoice_code',
        'consultation_id',
        'invoice_date',
        'consultation_fee',
        'lab_test_fee',
        'medicine_fee',
        'other_fee',
        'discount',
        'total_amount',
        'payment_method',
        'paid_at',
        'cashier_id',
        'status',
    ];

    // ========================
    // 🔄 CASTS
    // ========================
    protected $casts = [
        'invoice_date' => 'datetime',
        'paid_at' => 'datetime',
        'consultation_fee' => 'decimal:2',
        'lab_test_fee' => 'decimal:2',
        'medicine_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================
    // 🔥 CONSTANT (STATUS)
    // ========================
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = 2;

    // ========================
    // 🔥 CONSTANT (PAYMENT METHOD)
    // ========================
    const PAYMENT_CASH = 'Cash';
    const PAYMENT_CARD = 'Card';
    const PAYMENT_TRANSFER = 'Transfer';
    const PAYMENT_INSURANCE = 'Insurance';
    const PAYMENT_OTHER = 'Other';

    // ========================
    // 🔥 DEFAULT FEE
    // ========================
    const DEFAULT_CONSULTATION_FEE = 100000;

    // ========================
    // 🎯 ACCESSOR
    // ========================
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ thanh toán',
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    public function getPaymentMethodTextAttribute()
    {
        return match ($this->payment_method) {
            self::PAYMENT_CASH => 'Tiền mặt',
            self::PAYMENT_CARD => 'Thẻ',
            self::PAYMENT_TRANSFER => 'Chuyển khoản',
            self::PAYMENT_INSURANCE => 'Bảo hiểm',
            self::PAYMENT_OTHER => 'Khác',
            default => '---',
        };
    }

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }

    public function cashier()
    {
        return $this->belongsTo(Staff::class, 'cashier_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
    }

    // ========================
    // 🔍 SCOPE
    // ========================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Tạo mã hóa đơn tự động: HD-YYYYMMDD-XXXX
     */
    public static function generateInvoiceCode()
    {
        $today = now()->format('Ymd');
        $prefix = "HD-{$today}-";

        $latest = self::where('invoice_code', 'like', "{$prefix}%")
            ->orderBy('invoice_code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->invoice_code, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
