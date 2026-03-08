<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Fraud;
use App\Models\MasterKaryawan;

use Illuminate\Http\Request;

class FraudController extends Controller
{
    public function index()
    {
        return Inertia::render('fraud/Fraud');
    }

    public function data(Request $request)
    {
        $columns = ['tanggal', 'nik', 'master_karyawans.nama', 'fraud'];

        $query = Fraud::query()
        ->leftJoin('master_karyawans', 'frauds.nik', '=', 'master_karyawans.nik')
        ->select(
            'frauds.*',
            'master_karyawans.nama'
        );

        // Search
    if ($request->filled('search.value')) {
        $search = $request->input('search.value');

        $query->where(function ($q) use ($search) {
            $q->where('fraud', 'like', "%{$search}%")
              ->orWhere('tanggal', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
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
            "recordsTotal" => Fraud::count(),
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|max:20|unique:master_karyawans,nik',
            'tgl' => 'required|date',
            'fraud' => 'required',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'tgl.required' => 'Tanggal wajib diisi',
            'fraud.required' => 'Kronologi fraud wajib diisi',
        ]);

        Fraud::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = Fraud::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|max:20|unique:master_karyawans,nik,' . $id,
            'tgl' => 'required|date',
            'fraud' => 'required',
        ]);

        // $data->update($request->all());

        $data->update($validated);

        return redirect()->back();
    }

    public function delete($id)
    {
        Fraud::findOrFail($id)->delete();

        return redirect()->back();
    }

    public function getByNik($nik)
    {
        $karyawan = MasterKaryawan::where('nik', $nik)
            ->select('nik','nama')
            ->first();

        if (!$karyawan) {
            return response()->json(null);
        }

        return response()->json($karyawan);
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $data = MasterKaryawan::where('nik', 'like', "%{$search}%")
            ->orWhere('nama', 'like', "%{$search}%")
            ->select('nik','nama')
            ->limit(10)
            ->get();

        return response()->json($data);
    }
}
