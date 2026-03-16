<?php

namespace App\Imports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class AbsensiImport implements 
    ToModel, 
    WithHeadingRow, 
    WithChunkReading,
    WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $bulan;
    protected $tahun;

    public function __construct($bulan,$tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function model(array $row)
    {
        return new Absensi([
            'nik' => $row['nik'],
            'tanggal' => Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d'),
            'machine_in' => $row['machine_in'] ?? null,
            'machine_out' => $row['machine_out'] ?? null,
            'shiftcode' => $row['shiftcode'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
            'status_izin' => $row['status_izin'] ?? null,
            'ket_izin' => $row['ket_izin'] ?? null,
            'status_data' => 'draft'
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
