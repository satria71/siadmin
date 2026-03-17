<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use App\Imports\AbsensiImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        return Inertia::render('absensi/Absensi');
    }

    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls'
    //     ]);

    //     $rows = Excel::toArray([], $request->file('file'));

    //     if (!isset($rows[0][1][1])) {
    //         return back()->with('error','Kolom tanggal tidak ditemukan');
    //     }

    //     $tanggal = Date::excelToDateTimeObject($rows[0][1][1])->format('Y-m-d');

    //     $bulan = date('m', strtotime($tanggal));
    //     $tahun = date('Y', strtotime($tanggal));

    //     $cekFinal = DB::table('absensis')
    //         ->whereMonth('tanggal',$bulan)
    //         ->whereYear('tanggal',$tahun)
    //         ->where('status_data','final')
    //         ->exists();

    //     if ($cekFinal) {
    //         return back()->with('error','Data bulan ini sudah difinalisasi');
    //     }

    //     DB::table('absensis')
    //         ->whereMonth('tanggal',$bulan)
    //         ->whereYear('tanggal',$tahun)
    //         ->where('status_data','draft')
    //         ->delete();

    //     Excel::import(new AbsensiImport($bulan,$tahun), $request->file('file'));

    //     return back()->with('success','Import berhasil');
    // }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $rows = Excel::toArray([], $request->file('file'));

        $tanggal = Date::excelToDateTimeObject($rows[0][1][1])->format('Y-m-d');

        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        $cekFinal = DB::table('absensis')
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->where('status_data','final')
            ->exists();

        if ($cekFinal) {
            return back()->with('error','Data bulan ini sudah difinalisasi');
        }

        DB::table('absensis')
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->where('status_data','draft')
            ->delete();

        Excel::import(new AbsensiImport($bulan,$tahun), $request->file('file'));

        return back()->with('success','Import berhasil');
    }

    public function finalisasi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        DB::table('absensis')
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->update([
                'status_data' => 'final'
            ]);

        return back()->with('success','Data berhasil difinalisasi');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'nik',
            1 => 'nama',
            2 => 'jabatan',
            3 => 'jumlah_hadir',
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

    public function detail($nik)
    {
        $data = DB::table('absensis as a')
            ->leftJoin('shifts as s','a.shiftcode','=','s.shiftcode')
            ->selectRaw("
                a.tanggal,
                a.shiftcode,
                s.jam_masuk as normal_in,
                s.jam_pulang as normal_out,
                a.machine_in,
                a.machine_out,
                a.keterangan,

                /* HITUNG TERLAMBAT */
                CASE
                    WHEN a.machine_in IS NULL OR a.machine_in = '00:00:00'
                    THEN '00:00:00'
                    WHEN TIME(a.machine_in) > s.jam_masuk
                    THEN SEC_TO_TIME(TIMESTAMPDIFF(SECOND, s.jam_masuk, a.machine_in))
                    ELSE '00:00:00'
                END AS terlambat,

                /* HITUNG PULANG CEPAT */
                CASE
                    WHEN a.machine_out IS NULL OR a.machine_out = '00:00:00'
                    THEN '00:00:00'
                    WHEN TIME(a.machine_out) < s.jam_pulang
                    THEN SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.machine_out, s.jam_pulang))
                    ELSE '00:00:00'
                END AS pulang_cepat
            ")
            ->where('a.nik',$nik)
            ->orderBy('a.tanggal')
            ->get();

        return response()->json($data);
    }

    public function statistik()
    {
        $data = DB::table('absensis')
        ->selectRaw("
            SUM(CASE WHEN keterangan='Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN keterangan='Mangkir' THEN 1 ELSE 0 END) as mangkir,
            SUM(CASE WHEN keterangan='Libur' THEN 1 ELSE 0 END) as libur,
            SUM(CASE WHEN keterangan LIKE 'Lupa%' THEN 1 ELSE 0 END) as lupa_absen
        ")
        ->first();

        return response()->json($data);
    }

    public function chart()
    {
        $data = DB::table('absensis')
        ->selectRaw("
            DATE(tanggal) as tanggal,
            SUM(CASE WHEN keterangan='Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN keterangan='Mangkir' THEN 1 ELSE 0 END) as mangkir
        ")
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();

        return response()->json($data);
    }

    public function ranking()
    {
        $data = DB::table('absensis')
        ->selectRaw("
            nik,
            COUNT(CASE WHEN keterangan='Hadir' THEN 1 END) as hadir
        ")
        ->groupBy('nik')
        ->orderByDesc('hadir')
        ->limit(10)
        ->get();

        return response()->json($data);
    }

    public function heatmap()
    {
        $data = DB::table('absensis')
            ->select('tanggal','keterangan')
            ->orderBy('tanggal')
            ->get();

        return response()->json($data);
    }

    public function downloadFormat()
    {
        return Excel::download(new \App\Exports\AbsensiTemplateExport, 'format_absensi.xlsx');
    }
}
