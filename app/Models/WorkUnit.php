<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
