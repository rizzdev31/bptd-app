<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nip',
        'name',
        'email',
        'password',
        'role_id',
        'work_unit_id',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class, 'work_unit_id');
    }

    public function recipient(): HasOne
    {
        return $this->hasOne(Recipient::class, 'user_id');
    }

    public function isSuperadmin(): bool
    {
        return optional($this->role)->name === 'superadmin';
    }

    public function hasRole(string|array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roleNames)) {
            return in_array($this->role->name, $roleNames);
        }

        return $this->role->name === $roleNames;
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('name', $permissionName)->exists();
    }
}
