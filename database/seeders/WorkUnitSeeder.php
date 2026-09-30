<?php

namespace Database\Seeders;

use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class WorkUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'code' => 'TU',
                'name' => 'Subbagian Tata Usaha',
                'description' => 'Urusan kepegawaian, keuangan, perlengkapan, persuratan, dan rumah tangga.',
                'status' => 'active',
            ],
            [
                'code' => 'LLJ',
                'name' => 'Seksi Lalu Lintas Jalan',
                'description' => 'Manajemen dan rekayasa lalu lintas jalan antarkota/provinsi.',
                'status' => 'active',
            ],
            [
                'code' => 'SARPRAS',
                'name' => 'Seksi Sarana dan Prasarana Transportasi Jalan',
                'description' => 'Pembangunan, pemeliharaan, dan inventarisasi fasilitas perlengkapan jalan.',
                'status' => 'active',
            ],
            [
                'code' => 'WASGAKUM',
                'name' => 'Seksi Pengawasan dan Penegakan Hukum',
                'description' => 'Pengawasan operasional angkutan jalan, inspeksi keselamatan, dan penegakan hukum.',
                'status' => 'active',
            ],
            [
                'code' => 'SATPO-OPS',
                'name' => 'Satuan Pelayanan Operasional Terminal & UPPKB',
                'description' => 'Unit lapangan operasional terminal tipe A dan jembatan timbang Jawa Timur.',
                'status' => 'active',
            ],
        ];

        foreach ($units as $unit) {
            WorkUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}
