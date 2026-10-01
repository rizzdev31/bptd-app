<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_id',
        'item_id',
        'item_code',
        'item_name',
        'quantity',
        'unit',
        'conversion_factor',
        'base_quantity',
        'current_stock_before',
        'current_stock_after',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'conversion_factor' => 'integer',
        'base_quantity' => 'integer',
        'current_stock_before' => 'integer',
        'current_stock_after' => 'integer',
    ];

    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(StockOut::class, 'stock_out_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
