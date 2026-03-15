<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use App\Imports\AbsensiImport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        return Inertia::render('absensi/Absensi');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new AbsensiImport, $request->file('file'));

        return redirect()->back()->with('success','Import berhasil');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'a.nik',
            1 => 'k.nama',
            2 => 'k.jabatan',
            3 => 'k.jumlah_hadir',
            4 => 'jumlah_terlambat',
            5 => 'menit_terlambat',
            6 => 'jumlah_pulang_cepat',
            7 => 'menit_pulang_cepat'
        ];

        $query = DB::table(DB::raw("
        (
            SELECT
                a.nik,
                k.nama,
                k.jabatan,
                a.keterangan,

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

                    /* jika pulang lewat tengah malam */
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
            LEFT JOIN master_karyawans k ON a.nik = k.nik
        ) t
        "))
        ->selectRaw("
            nik,
            nama,
            jabatan,

            /* HADIR */
            SUM(CASE WHEN keterangan = 'Hadir' THEN 1 ELSE 0 END) AS jumlah_hadir,

            /* TERLAMBAT */
            SUM(
                CASE
                    WHEN machine_in IS NOT NULL
                    AND machine_in > shift_start
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_terlambat,

            COALESCE(
                SEC_TO_TIME(
                    SUM(
                        CASE
                            WHEN machine_in IS NOT NULL
                            AND machine_in > shift_start
                            THEN GREATEST(TIMESTAMPDIFF(SECOND, shift_start, machine_in),0)
                            ELSE 0
                        END
                    )
                ),
            '00:00:00') AS menit_terlambat,

            /* PULANG CEPAT */
            SUM(
                CASE
                    WHEN real_machine_out IS NOT NULL
                    AND real_machine_out < shift_end
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_pulang_cepat,

            COALESCE(
                SEC_TO_TIME(
                    SUM(
                        CASE
                            WHEN real_machine_out IS NOT NULL
                            AND real_machine_out < shift_end
                            THEN GREATEST(TIMESTAMPDIFF(SECOND, real_machine_out, shift_end),0)
                            ELSE 0
                        END
                    )
                ),
            '00:00:00') AS menit_pulang_cepat,

            /* FRAUD */
            SUM(CASE WHEN keterangan = 'Lupa Absen Masuk' THEN 1 ELSE 0 END) AS lam,
            SUM(CASE WHEN keterangan = 'Lupa Absen Pulang' THEN 1 ELSE 0 END) AS lap,
            SUM(CASE WHEN keterangan = 'Mangkir' THEN 1 ELSE 0 END) AS mangkir,

            /* TOTAL FRAUD */
            (
                SUM(
                    CASE
                        WHEN machine_in IS NOT NULL
                        AND machine_in > shift_start
                        THEN 1 ELSE 0
                    END
                )
                +
                SUM(
                    CASE
                        WHEN real_machine_out IS NOT NULL
                        AND real_machine_out < shift_end
                        THEN 1 ELSE 0
                    END
                )
                +
                SUM(CASE WHEN keterangan = 'Lupa Absen Masuk' THEN 1 ELSE 0 END)
                +
                SUM(CASE WHEN keterangan = 'Lupa Absen Pulang' THEN 1 ELSE 0 END)
                +
                SUM(CASE WHEN keterangan = 'Mangkir' THEN 1 ELSE 0 END)
            ) AS fraud
        ")
        ->groupBy('nik','nama','jabatan');

        // SEARCH
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // TOTAL
        $total = $query->get()->count();

        // ORDER
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'] - 1;
            $dir = $request->order[0]['dir'];

            if (isset($columns[$columnIndex])) {
                $query->orderBy($columns[$columnIndex], $dir);
            }
        }

        // PAGINATION
        $data = $query
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }
}
