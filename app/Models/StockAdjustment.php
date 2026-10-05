<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'type',
        'total_items',
        'reason',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'total_items' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockAdjustmentDetail::class, 'stock_adjustment_id');
    }

    /**
     * Generate unique adjustment number according to PRD format:
     * ADJ-YYYYMMDD-0001
     */
    public static function generateAdjustmentNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "ADJ-{$today}-";

        $last = self::where('adjustment_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->adjustment_number, -4);
            $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return "{$prefix}{$nextSeq}";
    }
}
