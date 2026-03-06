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
            'gudang' => 'DCI',
            'bagian' => 'Admin',
            'kelas' => 'Senior',
            'jabatan' => 'SPV',
            'tipe' => 'NON Driver',
            'status_kerja' => 'Aktif',
            'status_karyawan' => 'Tetap',
            'job_class' => 'B',
            'tgl_efektif' => '2025-10-10',
            'tgl_tetap' => '2025-10-10',
            'tgl_keluar' => '2025-10-10',
            'ket_masuk' => '',
            'ket_keluar' => '',
        ]);

        
    }
}
