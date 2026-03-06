<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKaryawan extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'nik',
        'nik_lama',
        'nama',
        'gudang',
        'bagian',
        'kelas',
        'jabatan',
        'tipe',
        'status_kerja',
        'status_karyawan',
        'job_class',
        'tgl_efektif',
        'tgl_tetap',
        'tgl_keluar',
        'ket_masuk',
        'ket_keluar'
    ];
}