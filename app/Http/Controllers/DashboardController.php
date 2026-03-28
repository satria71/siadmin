<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::guard('karyawan')->user();

    //     $absensi = DB::table('absensis')
    //         ->where('nik', $user->nik)
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     return Inertia::render('dashboard/Dashboard', [
    //         'absensi' => $absensi
    //     ]);
    // }
    
    public function index()
    {
        $user = Auth::guard('karyawan')->user();

        $data = DB::table(DB::raw("
            (
                SELECT
                    a.nik,
                    a.tanggal,
                    a.shiftcode,
                    a.keterangan,
                    s.jam_masuk,
                    s.jam_pulang,

                    /* MACHINE IN */
                    CASE
                        WHEN a.machine_in = '00:00:00' OR a.machine_in IS NULL
                        THEN NULL
                        ELSE TIMESTAMP(DATE(a.tanggal), a.machine_in)
                    END AS machine_in,

                    /* MACHINE OUT NORMALISASI */
                    CASE
                        WHEN a.machine_out = '00:00:00' OR a.machine_out IS NULL
                        THEN NULL
                        WHEN a.machine_out < s.jam_masuk
                        THEN TIMESTAMP(DATE(a.tanggal) + INTERVAL 1 DAY, a.machine_out)
                        ELSE TIMESTAMP(DATE(a.tanggal), a.machine_out)
                    END AS real_machine_out,

                    /* SHIFT START */
                    TIMESTAMP(DATE(a.tanggal), s.jam_masuk) AS shift_start,

                    /* SHIFT END */
                    CASE
                        WHEN s.jam_pulang < s.jam_masuk
                        THEN TIMESTAMP(DATE(a.tanggal) + INTERVAL 1 DAY, s.jam_pulang)
                        ELSE TIMESTAMP(DATE(a.tanggal), s.jam_pulang)
                    END AS shift_end

                FROM absensis a
                LEFT JOIN shifts s ON a.shiftcode = s.shiftcode
            ) t
        "))
        ->selectRaw("
            tanggal,
            shiftcode,

            jam_masuk AS normal_in,
            jam_pulang AS normal_out,

            TIME(machine_in) AS machine_in,
            TIME(real_machine_out) AS machine_out,

            keterangan AS status,

            /* TERLAMBAT */
            CASE
                WHEN machine_in IS NOT NULL
                AND machine_in > shift_start
                THEN SEC_TO_TIME(
                    GREATEST(TIMESTAMPDIFF(SECOND, shift_start, machine_in),0)
                )
                ELSE '00:00:00'
            END AS terlambat,

            /* PULANG CEPAT */
            CASE
                WHEN real_machine_out IS NOT NULL
                AND real_machine_out < shift_end
                THEN SEC_TO_TIME(
                    GREATEST(TIMESTAMPDIFF(SECOND, real_machine_out, shift_end),0)
                )
                ELSE '00:00:00'
            END AS pulang_cepat
        ")
        ->where('nik', $user->nik)
        ->orderBy('tanggal')
        ->get();

        return Inertia::render('dashboard/Dashboard', [
            'absensi' => $data
        ]);
    }
}
