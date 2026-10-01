<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'recipient_id',
        'recipient_name',
        'recipient_nip',
        'recipient_unit',
        'recipient_position',
        'user_id',
        'total_items',
        'total_quantity',
        'notes',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class, 'recipient_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockOutDetail::class, 'stock_out_id');
    }

    /**
     * Generate unique transaction number according to PRD format:
     * OUT-YYYYMMDD-0001
     */
    public static function generateTransactionNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "OUT-{$today}-";

        $last = self::where('transaction_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->transaction_number, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
