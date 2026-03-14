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
            1 => 'jumlah_terlambat',
            2 => 'menit_terlambat',
            3 => 'jumlah_pulang_cepat',
            4 => 'menit_pulang_cepat'
        ];

        $query = DB::table('absensis as a')
            ->leftJoin('shifts as s', 'a.shiftcode', '=', 's.shiftcode')
            ->selectRaw("
                a.nik,

                SUM(
                    CASE 
                        WHEN a.machine_in IS NOT NULL 
                        AND a.machine_in > s.jam_masuk 
                        THEN 1 ELSE 0
                    END
                ) AS jumlah_terlambat,

                SUM(
                    CASE 
                        WHEN a.machine_in IS NOT NULL 
                        AND a.machine_in > s.jam_masuk
                        THEN TIMESTAMPDIFF(MINUTE, s.jam_masuk, a.machine_in)
                        ELSE 0
                    END
                ) AS menit_terlambat,

                SUM(
                    CASE 
                        WHEN a.machine_out IS NOT NULL 
                        AND a.machine_out < s.jam_pulang 
                        THEN 1 ELSE 0
                    END
                ) AS jumlah_pulang_cepat,

                SUM(
                    CASE 
                        WHEN a.machine_out IS NOT NULL 
                        AND a.machine_out < s.jam_pulang
                        THEN TIMESTAMPDIFF(MINUTE, a.machine_out, s.jam_pulang)
                        ELSE 0
                    END
                ) AS menit_pulang_cepat
            ")
            ->groupBy('a.nik');

        // SEARCH
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where('a.nik', 'like', "%{$search}%");
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
