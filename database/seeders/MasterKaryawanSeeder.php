<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterKaryawan;

class MasterKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterKaryawan::create([
            'nik_lama' => '',
            'nik' => '2015401125',
            'nama' => 'Satria',
            'lokasi' => 'DCI',
            'bagian' => 'Admin',
            'jabatan' => 'SPV',
            'tipe' => 'NON Driver',
            'jc' => 'B',
            'status' => 'Kontrak',
            'tgl_efektif' => '2025-10-10',
            'tgl_tetap' => '2025-10-10',
            'tgl_keluar' => '2025-10-10',
            'status_kerja' => 'Aktif',
        ]);

        
    }
}
