<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FraudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fraud::create([
            'tanggal' => '2026-02-28',
            'nik' => '2015506800',
            'fraud' => 'Manipulasi data absensi',
        ]);
    }
}
