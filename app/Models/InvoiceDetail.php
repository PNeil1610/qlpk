<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_detail';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // ========================
    // 🧾 FILLABLE
    // ========================
    protected $fillable = [
        'invoice_id',
        'item_type',
        'item_name',
        'reference_id',
        'quantity',
        'unit_price',
        'discount',
        'line_total',
        'notes',
        'status',
    ];

    // ========================
    // 🔄 CASTS
    // ========================
    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================
    // 🔥 CONSTANT (ITEM TYPE)
    // ========================
    const TYPE_CONSULTATION = 'Consultation';
    const TYPE_LAB_TEST = 'LabTest';
    const TYPE_MEDICINE = 'Medicine';
    const TYPE_OTHER = 'Other';

    // ========================
    // 🔗 RELATIONSHIPS
    // ========================

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
