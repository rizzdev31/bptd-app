<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_number',
        'opname_date',
        'status',
        'total_items',
        'total_matched',
        'total_mismatched',
        'notes',
        'user_id',
        'conducted_by',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'total_items' => 'integer',
        'total_matched' => 'integer',
        'total_mismatched' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class, 'stock_opname_id');
    }

    /**
     * Generate unique opname number according to PRD format:
     * OPN-YYYYMMDD-0001
     */
    public static function generateOpnameNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "OPN-{$today}-";

        $last = self::where('opname_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->opname_number, -4);
            $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return "{$prefix}{$nextSeq}";
    }
}
