<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['code' => 'PCS', 'name' => 'Pcs', 'description' => 'Pieces / Satuan satuan', 'status' => 'active'],
            ['code' => 'BOX', 'name' => 'Box', 'description' => 'Kotak kemasan standar', 'status' => 'active'],
            ['code' => 'PACK', 'name' => 'Pack', 'description' => 'Bungkus / Pak', 'status' => 'active'],
            ['code' => 'RIM', 'name' => 'Rim', 'description' => 'Rim (500 lembar kertas)', 'status' => 'active'],
            ['code' => 'LUSIN', 'name' => 'Lusin', 'description' => 'Lusin (12 pcs)', 'status' => 'active'],
            ['code' => 'SET', 'name' => 'Set', 'description' => 'Satu set perlengkapan lengkap', 'status' => 'active'],
            ['code' => 'ROLL', 'name' => 'Roll', 'description' => 'Gulungan / Roll pita/lakban', 'status' => 'active'],
            ['code' => 'BUKU', 'name' => 'Buku', 'description' => 'Buku ekspedisi / agenda', 'status' => 'active'],
            ['code' => 'BOTOL', 'name' => 'Botol', 'description' => 'Botol tinta / isi ulang', 'status' => 'active'],
        ];

        foreach ($units as $u) {
            Unit::updateOrCreate(['code' => $u['code']], $u);
        }
    }
}
