<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id',
        'item_id',
        'item_name',
        'item_code',
        'quantity',
        'unit',
        'conversion_factor',
        'base_quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'conversion_factor' => 'integer',
        'base_quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_unit_price',
        'formatted_subtotal',
    ];

    /**
     * Relasi ke Header Pengadaan
     */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /**
     * Relasi ke Item ATK
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Accessor format Rupiah harga satuan
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->unit_price, 0, ',', '.');
    }

    /**
     * Accessor format Rupiah subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }
}
