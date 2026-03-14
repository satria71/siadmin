<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'nik',
        'tanggal',
        'machine_in',
        'machine_out',
        'shiftcode',
        'keterangan',
        'status_izin',
        'ket_izin',
        'status_data'
    ];
}
