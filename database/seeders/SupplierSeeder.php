<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-001',
                'name' => 'PT Graha Alat Tulis Mandiri',
                'address' => 'Jl. Basuki Rahmat No. 45, Genteng, Surabaya',
                'phone' => '031-5342111',
                'email' => 'sales@grahaatk.co.id',
                'contact_person' => 'Bambang Sudibyo',
                'tax_number' => '01.234.567.8-604.000',
                'status' => 'active',
                'notes' => 'Penyedia langganan pengadaan kertas dan alat tulis kantor kantor dinas',
            ],
            [
                'code' => 'SUP-002',
                'name' => 'CV Sinar Kencana Abadi',
                'address' => 'Kawasan Industri Rungkut Megah Raya Blok B-12, Surabaya',
                'phone' => '031-8419088',
                'email' => 'info@sinarkencana.co.id',
                'contact_person' => 'Hendra Wijaya',
                'tax_number' => '02.345.678.9-605.000',
                'status' => 'active',
                'notes' => 'Spesialis perlengkapan arsip, box file, dan cetak amplop dinas',
            ],
            [
                'code' => 'SUP-003',
                'name' => 'Toko Alat Tulis Berkah Jaya Sidoarjo',
                'address' => 'Jl. Pahlawan No. 88, Sidoarjo',
                'phone' => '031-8941200',
                'email' => 'berkahjaya.atk@gmail.com',
                'contact_person' => 'Hj. Mardiyah',
                'tax_number' => '03.456.789.0-606.000',
                'status' => 'active',
                'notes' => 'Penyedia pengadaan cepat ATK harian operasional staf',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(['code' => $s['code']], $s);
        }
    }
}
