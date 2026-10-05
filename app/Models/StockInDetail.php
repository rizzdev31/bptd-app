<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'item_id',
        'item_name',
        'item_code',
        'quantity',
        'unit',
        'conversion_factor',
        'base_quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'conversion_factor' => 'integer',
        'base_quantity' => 'integer',
    ];

    /**
     * Relasi ke Header Transaksi Masuk
     */
    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(StockIn::class);
    }

    /**
     * Relasi ke Item ATK
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
