<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\MasterKaryawan;

use Illuminate\Http\Request;

class MasterKaryawanController extends Controller
{
    public function index()
    {
        return Inertia::render('masterKaryawan/MasterKaryawan');
    }

    public function data(Request $request)
    {
        $columns = ['id','nik', 'nama', 'lokasi', 'bagian', 'status_kerja','tgl_efektif','tgl_tetap'];

        $query = MasterKaryawan::query();

        // Search
    if ($request->filled('search.value')) {
        $search = $request->input('search.value');

        $query->where(function ($q) use ($search) {
            $q->where('nik', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%")
              ->orWhere('gudang', 'like', "%{$search}%")
              ->orWhere('bagian', 'like', "%{$search}%")
              ->orWhere('status_kerja', 'like', "%{$search}%");
        });
    }

        $total = $query->count();

        // Order
        if ($request->order) {
            $columnIndex = $request->order[0]['column'] - 1;

            if (isset($columns[$columnIndex])) {
                $query->orderBy(
                    $columns[$columnIndex],
                    $request->order[0]['dir']
                );
            }
        }

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => MasterKaryawan::count(),
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function show($id)
    {
        return response()->json(
            MasterKaryawan::findOrFail($id)
        );
    }

    // public function tampilDetailKaryawan()
    // {
    //     return Inertia::render('masterKaryawan/DetailKaryawan');
    // }

    public function tampilDetailKaryawan($id)
    {
        $karyawan = MasterKaryawan::findOrFail($id);

        return Inertia::render('masterKaryawan/DetailKaryawan', [
            'karyawan' => $karyawan
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'nik' => 'required',
        'nama' => 'required',
    ]);

    MasterKaryawan::create([
        'nik' => $request->nik,
        'nik_lama' => $request->nik_lama,
        'nama' => $request->nama,
        'gudang' => $request->gudang,
        'bagian' => $request->bagian,
        'kelas' => $request->kelas,
        'jabatan' => $request->jabatan,
        'tipe' => $request->tipe,
        'status_kerja' => $request->status_kerja,
        'status_karyawan' => $request->status_karyawan,
        'job_class' => $request->job_class,
        'tgl_efektif' => $request->tgl_efektif,
        'tgl_tetap' => $request->tgl_tetap,
        'tgl_keluar' => $request->tgl_keluar,
        'ket_masuk' => $request->ket_masuk,
        'ket_keluar' => $request->ket_keluar,
    ]);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = MasterKaryawan::findOrFail($id);

        $data->update($request->all());

        return redirect()->back();
    }

    public function delete($id)
    {
        MasterKaryawan::findOrFail($id)->delete();

        return redirect()->back();
    }
}
