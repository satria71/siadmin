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
            'nik' => '123',
            'nama' => 'Putra', // boleh ada kalau kolomnya ada
            'bagian' => 'Admin', // boleh ada kalau kolomnya ada
            'password' => Hash::make('123'),
            'flag' => 'reguler',
        ]);
    }
}
