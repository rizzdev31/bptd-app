<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_number',
        'date',
        'supplier_id',
        'supplier_name',
        'status',
        'invoice_number',
        'total_amount',
        'payment_status',
        'notes',
        'user_id',
        'received_at',
        'received_by',
    ];

    protected $casts = [
        'date' => 'date',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected $appends = [
        'status_label',
        'status_badge_class',
        'formatted_total_amount',
    ];

    /**
     * Relasi ke Rincian Item Pengadaan
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProcurementDetail::class);
    }

    /**
     * Relasi ke Rekanan / Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke User pembuat pengadaan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke User penerima barang
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Relasi ke Riwayat Transaksi Fisik Stock In
     */
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    /**
     * Accessor label status bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft Dokumen',
            'ordered' => 'Dipesan (Ordered)',
            'received' => 'Diterima di Gudang',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Accessor styling badge status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
            'ordered' => 'bg-amber-50 text-amber-700 border-amber-200',
            'received' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Accessor format Rupiah total anggaran
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }
}
