<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik_lama');
            $table->string('nik');
            $table->string('nama');
            $table->string('gudang');
            $table->string('bagian');
            $table->string('kelas');
            $table->string('jabatan');
            $table->string('tipe');
            $table->string('status_kerja');
            $table->string('status_karyawan');
            $table->string('job_class');
            $table->date('tgl_efektif');
            $table->date('tgl_tetap');
            $table->date('tgl_keluar');
            $table->string('ket_masuk');
            $table->string('ket_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masterkaryawans');
    }
};
