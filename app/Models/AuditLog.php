<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_nip',
        'action',
        'module',
        'record_type',
        'record_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User pembuat aktivitas
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static untuk mencatat log audit secara instan dan otomatis
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        ?string $recordType = null,
        mixed $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): self {
        $currentUser = $user ?? auth()->user();
        
        $ip = null;
        $userAgent = null;
        try {
            $ip = request()->ip();
            $userAgent = request()->userAgent();
        } catch (\Throwable $e) {
            $ip = '127.0.0.1';
            $userAgent = 'CLI / System';
        }

        return self::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'Sistem / Tamu',
            'user_nip' => $currentUser?->nip,
            'action' => strtoupper(trim($action)),
            'module' => trim($module),
            'record_type' => $recordType ? class_basename($recordType) : null,
            'record_id' => $recordId !== null ? (string) $recordId : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Label aksi yang ramah pengguna
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'CREATE' => 'Tambah Data',
            'UPDATE' => 'Perubahan Data',
            'DELETE' => 'Hapus Data',
            'STATUS_CHANGE' => 'Ubah Status',
            'STOCK_OUT' => 'Distribusi SBPB',
            'STOCK_IN' => 'Penerimaan Stok',
            'ADJUSTMENT' => 'Penyesuaian Stok',
            'OPNAME' => 'Stock Opname',
            'RECEIVE' => 'Penerimaan PO',
            'CANCEL' => 'Pembatalan Transaksi',
            'LOGIN' => 'Login Masuk',
            'LOGOUT' => 'Logout Keluar',
            default => $this->action,
        };
    }

    /**
     * Badge kelas Tailwind untuk Aksi
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'CREATE', 'STOCK_IN', 'RECEIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-1 ring-emerald-500/20',
            'UPDATE', 'STATUS_CHANGE' => 'bg-blue-50 text-blue-700 border-blue-200 ring-1 ring-blue-500/20',
            'ADJUSTMENT', 'OPNAME' => 'bg-amber-50 text-amber-700 border-amber-200 ring-1 ring-amber-500/20',
            'STOCK_OUT' => 'bg-purple-50 text-purple-700 border-purple-200 ring-1 ring-purple-500/20',
            'DELETE', 'CANCEL' => 'bg-rose-50 text-rose-700 border-rose-200 ring-1 ring-rose-500/20',
            'LOGIN', 'LOGOUT' => 'bg-slate-100 text-slate-700 border-slate-200 ring-1 ring-slate-500/20',
            default => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }

    /**
     * Badge kelas Tailwind untuk Modul
     */
    public function getModuleBadgeClassAttribute(): string
    {
        return match ($this->module) {
            'Master ATK' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Permintaan ATK' => 'bg-purple-50 text-purple-700 border-purple-200',
            'Kendali Stok' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Pengadaan' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'Penerimaan Barang' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Pegawai' => 'bg-teal-50 text-teal-700 border-teal-200',
            'Role & Hak Akses' => 'bg-orange-50 text-orange-700 border-orange-200',
            'Autentikasi' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }
}
