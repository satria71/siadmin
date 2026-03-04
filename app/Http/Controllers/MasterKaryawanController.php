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
        $columns = ['nik', 'nama', 'lokasi', 'bagian', 'status_kerja','tgl_efektif','tgl_tetap'];

        $query = MasterKaryawan::query();

        // Search
    if ($request->filled('search.value')) {
        $search = $request->input('search.value');

        $query->where(function ($q) use ($search) {
            $q->where('nik', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%")
              ->orWhere('lokasi', 'like', "%{$search}%")
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
}
