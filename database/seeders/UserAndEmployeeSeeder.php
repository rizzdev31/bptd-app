<?php

namespace Database\Seeders;

use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::where('name', 'superadmin')->first();
        $petugasRole = Role::where('name', 'petugas')->first();
        $tuUnit = WorkUnit::where('code', 'TU')->first();
        $lljUnit = WorkUnit::where('code', 'LLJ')->first();
        $sarprasUnit = WorkUnit::where('code', 'SARPRAS')->first();
        $wasgakumUnit = WorkUnit::where('code', 'WASGAKUM')->first();
        $opsUnit = WorkUnit::where('code', 'SATPO-OPS')->first();

        // 1. Akun Superadmin (Bisa login dengan NIP atau Username)
        $superadminUser = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'nip' => '198501012010121001',
                'name' => 'Administrator BPTD Jatim',
                'email' => 'admin@bptd-jatim.dephub.go.id',
                'password' => Hash::make('password123'),
                'role_id' => $superadminRole?->id,
                'work_unit_id' => $tuUnit?->id,
                'phone' => '081234567890',
                'status' => 'active',
            ]
        );

        // Kaitkan sebagai Pegawai BPTD
        Recipient::firstOrCreate(
            ['nip' => '198501012010121001'],
            [
                'name' => 'Administrator BPTD Jatim',
                'work_unit_id' => $tuUnit?->id,
                'position' => 'Superadmin Pengelola Sistem',
                'phone' => '081234567890',
                'email' => 'admin@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'user_id' => $superadminUser->id,
                'description' => 'Akun Superadmin Utama aplikasi BPTD Kelas II Jawa Timur.',
            ]
        );

        // 2. Akun Petugas ATK Lapangan
        $petugasUser = User::firstOrCreate(
            ['username' => 'petugas'],
            [
                'nip' => '199203152018011002',
                'name' => 'Budi Santoso, S.Kom',
                'email' => 'budi.petugas@bptd-jatim.dephub.go.id',
                'password' => Hash::make('password123'),
                'role_id' => $petugasRole?->id,
                'work_unit_id' => $tuUnit?->id,
                'phone' => '082198765432',
                'status' => 'active',
            ]
        );

        Recipient::firstOrCreate(
            ['nip' => '199203152018011002'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'work_unit_id' => $tuUnit?->id,
                'position' => 'Pengelola Pengadaan & Persediaan ATK',
                'phone' => '082198765432',
                'email' => 'budi.petugas@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'user_id' => $petugasUser->id,
                'description' => 'Petugas operasional distribusi dan inventaris ATK.',
            ]
        );

        // 3. Master Pegawai BPTD (Penerima ATK di unit-unit kerja yang tidak perlu login)
        $employees = [
            [
                'nip' => '198007202005021001',
                'name' => 'Ir. Joko Prakoso, M.T.',
                'work_unit_id' => $sarprasUnit?->id,
                'position' => 'Kepala Seksi Sarana dan Prasarana',
                'phone' => '081122334455',
                'email' => 'joko.prakoso@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'description' => 'PNS BPTD Kelas II Jatim',
            ],
            [
                'nip' => '198811122011012003',
                'name' => 'Siti Aminah, S.E.',
                'work_unit_id' => $tuUnit?->id,
                'position' => 'Pengadministrasi Keuangan',
                'phone' => '081333444555',
                'email' => 'siti.aminah@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'description' => 'PNS Subbagian Tata Usaha',
            ],
            [
                'nip' => '199406082019021004',
                'name' => 'Rendy Pratama, A.Md.',
                'work_unit_id' => $lljUnit?->id,
                'position' => 'Pengatur Lalu Lintas Jalan',
                'phone' => '085677889900',
                'email' => 'rendy.pratama@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'description' => 'Staf Teknis Seksi LLJ',
            ],
            [
                'nip' => '199104252015032002',
                'name' => 'Dewi Lestari, S.Tr.Tra',
                'work_unit_id' => $wasgakumUnit?->id,
                'position' => 'Penyidik & Petugas Pengawasan Angkutan',
                'phone' => '087811223344',
                'email' => 'dewi.lestari@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'description' => 'Staf Teknis Wasgakum',
            ],
            [
                'nip' => '199512142022031005',
                'name' => 'Fajar Hidayat',
                'work_unit_id' => $opsUnit?->id,
                'position' => 'Petugas Layanan Operasional Terminal',
                'phone' => '089900112233',
                'email' => 'fajar.hidayat@bptd-jatim.dephub.go.id',
                'status' => 'active',
                'description' => 'Petugas Pelayanan Lapangan Terminal Tipe A',
            ],
        ];

        foreach ($employees as $emp) {
            Recipient::firstOrCreate(['nip' => $emp['nip']], $emp);
        }
    }
}
