<?php

namespace App\Imports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsensiImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Absensi([
            'nik' => $row['nik'],
            'tanggal' => Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d'),
            'normal_in' => $row['normal_in'],
            'normal_out' => $row['normal_out'],
            'machine_in' => $row['machine_in'],
            'machine_out' => $row['machine_out'],
            'shiftcode' => $row['shiftcode'],
            'keterangan' => $row['keterangan'],
            'status_izin' => $row['status_izin'],
            'ket_izin' => $row['ket_izin'],
            'status_data' => 'draft'
        ]);
    }
}
