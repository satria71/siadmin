<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fraud extends Model
{
    public $timestamps = false;
    protected $fillable = ['tanggal', 'nik', 'fraud'];
}
