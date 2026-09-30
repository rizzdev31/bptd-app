<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'KAT-01',
                'name' => 'Alat Tulis',
                'description' => 'Pulpen, pensil, spidol whiteboard/permanen, penghapus, koreksi pita',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-02',
                'name' => 'Kertas',
                'description' => 'Kertas HVS A4, F4, continuous form, karton, sticky notes',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-03',
                'name' => 'Map & Folder',
                'description' => 'Map snelhecter kertas/plastik, map gantung, stopmap folio, clear holder',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-04',
                'name' => 'Bahan Cetak',
                'description' => 'Amplop dinas BPTD berkop, buku nota ekspedisi dinas, form pengawasan',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-05',
                'name' => 'Perlengkapan Meja',
                'description' => 'Stapler, pelubang kertas (perforator), gunting kantor, cutter, klip kertas',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-06',
                'name' => 'Perlengkapan Arsip',
                'description' => 'Box file bindex, ordner kwitansi, pembatas dokumen, label binder',
                'status' => 'active',
            ],
            [
                'code' => 'KAT-07',
                'name' => 'Lainnya',
                'description' => 'Baterai alkaline, lem kertas cair/stick, lakban bening/coklat',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['code' => $cat['code']], $cat);
        }
    }
}
