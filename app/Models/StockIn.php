<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'date',
        'source',
        'procurement_id',
        'supplier_id',
        'supplier_name',
        'reference_number',
        'user_id',
        'notes',
        'total_items',
        'total_quantity',
    ];

    protected $casts = [
        'date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
    ];

    /**
     * Relasi ke Rincian Item Masuk
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockInDetail::class);
    }

    /**
     * Relasi ke Dokumen Pengadaan (jika ada)
     */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /**
     * Relasi ke Rekanan / Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke Petugas Penerima
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
