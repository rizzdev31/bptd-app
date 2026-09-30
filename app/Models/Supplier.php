<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'contact_person',
        'tax_number',
        'status',
        'notes',
    ];

    /**
     * Relasi ke Master ATK (Items)
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Scope untuk supplier aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
