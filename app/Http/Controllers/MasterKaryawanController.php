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
        // $request->validate([
        //     'nik' => 'required',
        //     'nama' => 'required',
        // ]);

        $validated = $request->validate([
            'nik' => 'required|max:20|unique:master_karyawans,nik',
            'nama' => 'required|max:100',
            'gudang' => 'required',
            'bagian' => 'required',
            'kelas' => 'required',
            'jabatan' => 'required',
            'jabatan_ho' => 'required',
            'tipe' => 'required',
            'status_kerja' => 'required',
            'status_karyawan' => 'required',
            'job_class' => 'required',
            'tgl_efektif' => 'required|date',
            'tgl_tetap' => 'nullable|date',
            'tgl_keluar' => 'nullable|date',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama.required' => 'Nama karyawan wajib diisi',
            'gudang.required' => 'Gudang harus dipilih',
            'bagian.required' => 'Bagian harus dipilih',
            'kelas.required' => 'Kelas harus dipilih',
            'jabatan.required' => 'Jabatan harus dipilih',
            'jabatan_ho.required' => 'Jabatan harus dipilih',
            'status_kerja.required' => 'Status kerja harus dipilih',
            'status_karyawan.required' => 'Status karyawan harus dipilih',
            'tgl_efektif.required' => 'Tanggal efektif wajib diisi',
            'job_class.required' => 'Job class wajib diisi',
            'tipe.required' => 'Tipe wajib diisi'
        ]);

        MasterKaryawan::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = MasterKaryawan::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|max:20|unique:master_karyawans,nik,' . $id,
            'nama' => 'required|max:100',
            'gudang' => 'required',
            'bagian' => 'required',
            'kelas' => 'required',
            'jabatan' => 'required',
            'jabatan_ho' => 'required',
            'tipe' => 'required',
            'status_kerja' => 'required',
            'status_karyawan' => 'required',
            'job_class' => 'required',
            'tgl_efektif' => 'required|date',
            'tgl_tetap' => 'nullable|date',
            'tgl_keluar' => 'nullable|date',
        ]);

        // $data->update($request->all());

        $data->update($validated);

        return redirect()->back();
    }

    public function delete($id)
    {
        MasterKaryawan::findOrFail($id)->delete();

        return redirect()->back();
    }
}
