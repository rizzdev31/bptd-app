<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $superadmin = Role::firstOrCreate(
            ['name' => 'superadmin'],
            [
                'label' => 'Superadmin / Petugas Utama',
                'description' => 'Memiliki hak akses penuh ke seluruh modul sistem ATK dan manajemen pengguna.',
            ]
        );

        $petugas = Role::firstOrCreate(
            ['name' => 'petugas'],
            [
                'label' => 'Petugas ATK',
                'description' => 'Mencatat distribusi, transaksi Stock In, Stock Out, dan opname stok.',
            ]
        );

        $pimpinan = Role::firstOrCreate(
            ['name' => 'pimpinan'],
            [
                'label' => 'Pimpinan / Kepala Balai',
                'description' => 'Memantau dashboard statistik stok dan laporan rekapitulasi ATK.',
            ]
        );

        // 2. Create Permissions
        $permissions = [
            // Pegawai / Master
            ['name' => 'pegawai.view', 'label' => 'Melihat Data Pegawai', 'module' => 'pegawai'],
            ['name' => 'pegawai.manage', 'label' => 'Mengelola Data Pegawai', 'module' => 'pegawai'],

            // Inventory & Master ATK
            ['name' => 'inventory.view', 'label' => 'Melihat Master ATK & Stok', 'module' => 'inventory'],
            ['name' => 'inventory.manage', 'label' => 'Mengelola Master ATK & Kategori', 'module' => 'inventory'],

            // Stock Transactions
            ['name' => 'stock_out.create', 'label' => 'Mencatat Pengeluaran ATK (Stock Out)', 'module' => 'transaksi'],
            ['name' => 'stock_out.view', 'label' => 'Melihat Riwayat Pengeluaran ATK', 'module' => 'transaksi'],
            ['name' => 'stock_in.create', 'label' => 'Mencatat Penerimaan ATK (Stock In)', 'module' => 'transaksi'],
            ['name' => 'stock_in.view', 'label' => 'Melihat Riwayat Penerimaan ATK', 'module' => 'transaksi'],
            ['name' => 'stock_opname.manage', 'label' => 'Melakukan Stock Opname & Adjustment', 'module' => 'transaksi'],

            // Reports
            ['name' => 'reports.view', 'label' => 'Melihat Laporan Stok & Distribusi', 'module' => 'laporan'],
            ['name' => 'reports.export', 'label' => 'Mengekspor Laporan ke Excel', 'module' => 'laporan'],

            // AI & System
            ['name' => 'ai.chat', 'label' => 'Menggunakan AI Stock Assistant', 'module' => 'ai'],
            ['name' => 'users.manage', 'label' => 'Mengelola Pengguna & Hak Akses', 'module' => 'system'],
            ['name' => 'audit.view', 'label' => 'Melihat Jejak Audit (Audit Trail)', 'module' => 'system'],
        ];

        $permissionIds = [];
        foreach ($permissions as $perm) {
            $created = Permission::firstOrCreate(['name' => $perm['name']], $perm);
            $permissionIds[$perm['name']] = $created->id;
        }

        // 3. Assign Permissions
        // Superadmin gets ALL permissions
        $superadmin->permissions()->sync(array_values($permissionIds));

        // Petugas gets inventory, stock in/out, opname, reports, ai
        $petugasPerms = [
            'pegawai.view', 'inventory.view', 'inventory.manage',
            'stock_out.create', 'stock_out.view',
            'stock_in.create', 'stock_in.view', 'stock_opname.manage',
            'reports.view', 'reports.export', 'ai.chat',
        ];
        $petugas->permissions()->sync(
            array_intersect_key($permissionIds, array_flip($petugasPerms))
        );

        // Pimpinan gets dashboard, reports, view only, ai
        $pimpinanPerms = [
            'pegawai.view', 'inventory.view', 'stock_out.view', 'stock_in.view',
            'reports.view', 'reports.export', 'ai.chat',
        ];
        $pimpinan->permissions()->sync(
            array_intersect_key($permissionIds, array_flip($pimpinanPerms))
        );
    }
}
