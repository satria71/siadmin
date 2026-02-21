<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Karyawan::create([
            'nik' => '2015496800',
            'nama' => 'Satria', // boleh ada kalau kolomnya ada
            'bagian' => 'SPV', // boleh ada kalau kolomnya ada
            'password' => Hash::make('125'),
            'flag' => 'admin',
        ]);
    }
}
