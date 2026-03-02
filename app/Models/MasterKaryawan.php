<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKaryawan extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'nik_lama',
        'nik',
        'nama',
        'lokasi',
        'bagian',
        'jabatan',
        'tipe',
        'jc',
        'status',
        'tgl_efektif',
        'tgl_tetap',
        'tgl_keluar',
        'status_kerja',
    ];
}