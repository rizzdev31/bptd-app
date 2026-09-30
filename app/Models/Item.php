<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'unit',
        'minimum_stock',
        'target_stock',
        'current_stock',
        'storage_location',
        'supplier_id',
        'status',
        'description',
    ];

    protected $casts = [
        'minimum_stock' => 'integer',
        'target_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    protected $appends = [
        'stock_status',
        'stock_status_label',
        'stock_percentage',
    ];

    /**
     * Relasi ke Kategori ATK
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Satuan ATK
     */
    public function unitDetail(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi ke Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Evaluasi Status Stok Sesuai PRD Seksi 16:
     * - OUT OF STOCK: current_stock == 0
     * - LOW STOCK: current_stock <= minimum_stock AND current_stock > 0
     * - AVAILABLE: current_stock > minimum_stock
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        if ($this->current_stock <= $this->minimum_stock) {
            return 'low_stock';
        }

        return 'available';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Stok Habis',
            'low_stock' => 'Stok Menipis',
            default => 'Stok Tersedia',
        };
    }

    public function getStockPercentageAttribute(): int
    {
        $target = $this->target_stock > 0 ? $this->target_stock : max(1, $this->minimum_stock * 2);
        return (int) min(100, round(($this->current_stock / $target) * 100));
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->whereColumn('current_stock', '>', 'minimum_stock');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock')
                     ->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }
}
