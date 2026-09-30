<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catKertas = Category::where('code', 'KAT-02')->first();
        $catAlatTulis = Category::where('code', 'KAT-01')->first();
        $catMap = Category::where('code', 'KAT-03')->first();
        $catCetak = Category::where('code', 'KAT-04')->first();
        $catMeja = Category::where('code', 'KAT-05')->first();
        $catArsip = Category::where('code', 'KAT-06')->first();

        $unitRim = Unit::where('code', 'RIM')->first();
        $unitBox = Unit::where('code', 'BOX')->first();
        $unitPcs = Unit::where('code', 'PCS')->first();
        $unitPack = Unit::where('code', 'PACK')->first();
        $unitLusin = Unit::where('code', 'LUSIN')->first();
        $unitSet = Unit::where('code', 'SET')->first();
        $unitBuku = Unit::where('code', 'BUKU')->first();

        $sup1 = Supplier::where('code', 'SUP-001')->first();
        $sup2 = Supplier::where('code', 'SUP-002')->first();
        $sup3 = Supplier::where('code', 'SUP-003')->first();

        $items = [
            [
                'code' => 'ATK-2026-0001',
                'barcode' => '899123456001',
                'name' => 'Kertas HVS A4 80gr Sinar Dunia',
                'category_id' => $catKertas?->id,
                'unit_id' => $unitRim?->id,
                'unit' => 'Rim',
                'minimum_stock' => 20,
                'target_stock' => 100,
                'current_stock' => 65,
                'storage_location' => 'Gudang ATK - Rak A1',
                'supplier_id' => $sup1?->id,
                'status' => 'active',
                'description' => 'Kertas resmi untuk surat dinas dan persuratan resmi BPTD',
            ],
            [
                'code' => 'ATK-2026-0002',
                'barcode' => '899123456002',
                'name' => 'Kertas HVS F4 70gr PaperOne',
                'category_id' => $catKertas?->id,
                'unit_id' => $unitRim?->id,
                'unit' => 'Rim',
                'minimum_stock' => 15,
                'target_stock' => 80,
                'current_stock' => 8, // Low stock
                'storage_location' => 'Gudang ATK - Rak A2',
                'supplier_id' => $sup1?->id,
                'status' => 'active',
                'description' => 'Kertas folio untuk lampiran berita acara dan draf SK pimpinan',
            ],
            [
                'code' => 'ATK-2026-0003',
                'barcode' => '899123456003',
                'name' => 'Pulpen Standard AE7 0.5 Hitam',
                'category_id' => $catAlatTulis?->id,
                'unit_id' => $unitBox?->id,
                'unit' => 'Box',
                'minimum_stock' => 10,
                'target_stock' => 50,
                'current_stock' => 32,
                'storage_location' => 'Lemari 1 - Laci 2',
                'supplier_id' => $sup3?->id,
                'status' => 'active',
                'description' => 'Pulpen ballpoint reguler untuk operasional staf kantor harian',
            ],
            [
                'code' => 'ATK-2026-0004',
                'barcode' => '899123456004',
                'name' => 'Pulpen Gel Pilot G2 0.7 Hitam',
                'category_id' => $catAlatTulis?->id,
                'unit_id' => $unitLusin?->id,
                'unit' => 'Lusin',
                'minimum_stock' => 5,
                'target_stock' => 25,
                'current_stock' => 3, // Low stock
                'storage_location' => 'Lemari 1 - Laci 3',
                'supplier_id' => $sup1?->id,
                'status' => 'active',
                'description' => 'Pulpen tanda tangan pimpinan dan pejabat pembuat komitmen (PPK)',
            ],
            [
                'code' => 'ATK-2026-0005',
                'barcode' => '899123456005',
                'name' => 'Spidol Whiteboard Snowman Hitam',
                'category_id' => $catAlatTulis?->id,
                'unit_id' => $unitLusin?->id,
                'unit' => 'Lusin',
                'minimum_stock' => 6,
                'target_stock' => 30,
                'current_stock' => 14,
                'storage_location' => 'Lemari 1 - Laci 4',
                'supplier_id' => $sup3?->id,
                'status' => 'active',
                'description' => 'Spidol papan tulis ruang rapat dan briefing lapangan BPTD',
            ],
            [
                'code' => 'ATK-2026-0006',
                'barcode' => '899123456006',
                'name' => 'Spidol Marker Permanen Snowman Biru',
                'category_id' => $catAlatTulis?->id,
                'unit_id' => $unitPcs?->id,
                'unit' => 'Pcs',
                'minimum_stock' => 15,
                'target_stock' => 50,
                'current_stock' => 0, // Out of stock
                'storage_location' => 'Lemari 1 - Laci 5',
                'supplier_id' => $sup3?->id,
                'status' => 'active',
                'description' => 'Spidol permanen untuk penomoran kardus arsip dan pelabelan barang',
            ],
            [
                'code' => 'ATK-2026-0007',
                'barcode' => '899123456007',
                'name' => 'Map Snelhecter Kertas Folio Biru',
                'category_id' => $catMap?->id,
                'unit_id' => $unitPack?->id,
                'unit' => 'Pack',
                'minimum_stock' => 10,
                'target_stock' => 40,
                'current_stock' => 22,
                'storage_location' => 'Rak B - Sekat 1',
                'supplier_id' => $sup2?->id,
                'status' => 'active',
                'description' => 'Map berkas administrasi pengawasan dan penegakan hukum',
            ],
            [
                'code' => 'ATK-2026-0008',
                'barcode' => '899123456008',
                'name' => 'Map Plastik L-Folder Transparan A4',
                'category_id' => $catMap?->id,
                'unit_id' => $unitPack?->id,
                'unit' => 'Pack',
                'minimum_stock' => 8,
                'target_stock' => 30,
                'current_stock' => 0, // Out of stock
                'storage_location' => 'Rak B - Sekat 3',
                'supplier_id' => $sup2?->id,
                'status' => 'active',
                'description' => 'Map plastik bening pelindung dokumen disposisi penting',
            ],
            [
                'code' => 'ATK-2026-0009',
                'barcode' => '899123456009',
                'name' => 'Box File Bindex Jumbo Biru Tua',
                'category_id' => $catArsip?->id,
                'unit_id' => $unitPcs?->id,
                'unit' => 'Pcs',
                'minimum_stock' => 12,
                'target_stock' => 50,
                'current_stock' => 28,
                'storage_location' => 'Rak C - Baris 2',
                'supplier_id' => $sup2?->id,
                'status' => 'active',
                'description' => 'Penyimpanan arsip laporan bulanan seksi dan subbagian',
            ],
            [
                'code' => 'ATK-2026-0010',
                'barcode' => '899123456010',
                'name' => 'Stapler Max HD-10D & Isi Staples No. 10',
                'category_id' => $catMeja?->id,
                'unit_id' => $unitSet?->id,
                'unit' => 'Set',
                'minimum_stock' => 5,
                'target_stock' => 20,
                'current_stock' => 11,
                'storage_location' => 'Lemari 2 - Rak 1',
                'supplier_id' => $sup3?->id,
                'status' => 'active',
                'description' => 'Paket hekter standar meja kantor dinas',
            ],
            [
                'code' => 'ATK-2026-0011',
                'barcode' => '899123456011',
                'name' => 'Buku Ekspedisi Surat Keluar BPTD',
                'category_id' => $catCetak?->id,
                'unit_id' => $unitBuku?->id,
                'unit' => 'Buku',
                'minimum_stock' => 5,
                'target_stock' => 20,
                'current_stock' => 2, // Low stock
                'storage_location' => 'Lemari Arsip TU',
                'supplier_id' => $sup2?->id,
                'status' => 'active',
                'description' => 'Buku tanda terima kurir dan pengiriman surat resmi',
            ],
            [
                'code' => 'ATK-2026-0012',
                'barcode' => '899123456012',
                'name' => 'Amplop Putih Berkop BPTD Kelas II Jatim',
                'category_id' => $catCetak?->id,
                'unit_id' => $unitBox?->id,
                'unit' => 'Box',
                'minimum_stock' => 10,
                'target_stock' => 40,
                'current_stock' => 18,
                'storage_location' => 'Gudang ATK - Rak B2',
                'supplier_id' => $sup2?->id,
                'status' => 'active',
                'description' => 'Amplop dinas resmi ukuran panjang berkop Kemenhub BPTD',
            ],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
