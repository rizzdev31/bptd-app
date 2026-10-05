<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'small_unit',
        'conversion_rate',
        'minimum_stock',
        'target_stock',
        'current_stock',
        'storage_location',
        'supplier_id',
        'status',
        'description',
    ];

    protected $casts = [
        'conversion_rate' => 'integer',
        'minimum_stock' => 'integer',
        'target_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    protected $appends = [
        'stock_status',
        'stock_status_label',
        'stock_percentage',
        'effective_small_unit',
        'formatted_stock',
        'formatted_minimum_stock',
        'has_multi_unit',
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
     * Relasi ke Rincian Pengadaan (Procurements)
     */
    public function procurementDetails(): HasMany
    {
        return $this->hasMany(ProcurementDetail::class);
    }

    /**
     * Relasi ke Rincian Penerimaan Fisik (Stock In)
     */
    public function stockInDetails(): HasMany
    {
        return $this->hasMany(StockInDetail::class);
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
     * Satuan eceran / terkecil efektif (default ke Pcs atau unit)
     */
    public function getEffectiveSmallUnitAttribute(): string
    {
        return $this->small_unit ?: ($this->unit ?: 'Pcs');
    }

    /**
     * Cek apakah barang memiliki satuan bertingkat (misal Lusin -> Pcs, Box -> Pcs, Pack -> Pcs)
     */
    public function getHasMultiUnitAttribute(): bool
    {
        return (int) $this->conversion_rate > 1 && 
               strcasecmp($this->unit ?? '', $this->effective_small_unit) !== 0;
    }

    /**
     * Format tampilan stok ramah pengguna:
     * E.g. 36 Pcs (1 Lusin = 12) -> "3 Lusin (36 Pcs)"
     * E.g. 34 Pcs (1 Lusin = 12) -> "2 Lusin 10 Pcs"
     * E.g. 5 Pcs (1 Lusin = 12) -> "5 Pcs"
     */
    public function getFormattedStockAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return '0 ' . ($this->unit ?: 'Pcs');
        }

        $rate = max(1, (int) $this->conversion_rate);
        $small = $this->effective_small_unit;
        $pkgUnit = $this->unit ?: 'Pcs';

        if (!$this->has_multi_unit) {
            return number_format($this->current_stock, 0, ',', '.') . ' ' . $pkgUnit;
        }

        $wholePkg = intdiv((int) ($this->current_stock ?? 0), $rate);
        $rem = (int) ($this->current_stock ?? 0) % $rate;

        if ($wholePkg > 0 && $rem > 0) {
            return "{$wholePkg} {$pkgUnit} {$rem} {$small}";
        }

        if ($wholePkg > 0 && $rem === 0) {
            return "{$wholePkg} {$pkgUnit} (" . number_format($this->current_stock, 0, ',', '.') . " {$small})";
        }

        return "{$rem} {$small} (Eceran)";
    }

    /**
     * Format stok minimum
     */
    public function getFormattedMinimumStockAttribute(): string
    {
        $rate = max(1, (int) $this->conversion_rate);
        $pkgUnit = $this->unit ?: 'Pcs';
        $small = $this->effective_small_unit;
        $minStock = (int) ($this->minimum_stock ?? 0);

        if ($minStock <= 0) {
            return "0 " . $pkgUnit;
        }

        if (!$this->has_multi_unit) {
            return $minStock . ' ' . $pkgUnit;
        }

        $wholePkg = intdiv($minStock, $rate);
        $rem = $minStock % $rate;

        if ($rem === 0 && $wholePkg > 0) {
            return "{$wholePkg} {$pkgUnit}";
        }

        return "{$this->minimum_stock} {$small}";
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

    public function stockOutDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockOutDetail::class, 'item_id');
    }

    public function stockLedgers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockLedger::class, 'item_id');
    }

    public function stockAdjustmentDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockAdjustmentDetail::class, 'item_id');
    }

    public function stockOpnameDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockOpnameDetail::class, 'item_id');
    }
}
