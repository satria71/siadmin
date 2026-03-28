<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $master = DB::table('master_karyawans')->get();

        foreach ($master as $row) {

            // ambil 5 digit terakhir dari nik
            $passwordPlain = substr($row->nik, -5);

            DB::table('karyawans')->updateOrInsert(
                ['nik' => $row->nik],
                [
                    'nama' => $row->nama,
                    'bagian' => $row->bagian,
                    'password' => Hash::make(substr($row->nik, -5)),
                    'flag' => 'user',
                ]
            );
        }
    }
}
