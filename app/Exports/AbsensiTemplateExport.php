<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiTemplateExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect ([
            ['2015496800','2026-03-01', '00:00:00', '00:00:00', '08:05:00', '17:00:00', 'Hadir','Libur','-','-'],
        ]);
    }

    public function headings(): array
    {
        return [
            'NIK',
            'TANGGAL',
            'NORMAL IN',
            'NORMAL OUT',
            'MACHINE IN',
            'MACHINE OUT',
            'SHIFTCODE',
            'KETERANGAN',
            'STATUS IZIN',
            'KET IZIN',
        ];
    }

    // public function array(): array
    // {
    //     return [
    //         ['2015496800','2026-03-01', '00:00:00', '00:00:00', '08:05:00', '17:00:00', 'Hadir','Libur','-','-'],
    //     ];
    // }
}
