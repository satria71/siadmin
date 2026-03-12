<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Fraud;
use App\Models\MasterKaryawan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FraudController extends Controller
{
    public function index()
    {
        return Inertia::render('fraud/Fraud');
    }

    public function data(Request $request)
    {
        $columns = ['tanggal', 'nik', 'master_karyawans.nama', 'master_karyawans.bagian', 'fraud'];

        $query = Fraud::query()
        ->leftJoin('master_karyawans', 'frauds.nik', '=', 'master_karyawans.nik')
        ->select(
            'frauds.*',
            'master_karyawans.nama',
            'master_karyawans.bagian'
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
            ->get()
            ->map(function ($item) {

                $item->file_exists = $item->file_pdf
                    ? Storage::disk('public')->exists('fraud/'.$item->file_pdf)
                    : false;

                return $item;
            });

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
            'nik' => 'required|max:20',
            'tanggal' => 'required|date',
            'fraud' => 'required',
            'file_pdf' => 'nullable|mimes:pdf|max:2048'
        ], [
            'nik.required' => 'NIK wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'fraud.required' => 'Kronologi fraud wajib diisi',
            'file_pdf.mimes' => 'Ukuran max file 2mb'
        ]);

        if ($request->hasFile('file_pdf')) {

            $file = $request->file('file_pdf');

            $nik = $request->nik;
            $tanggal = date('Ymd', strtotime($request->tanggal));

            $filename = "FRD_{$nik}_{$tanggal}.".$file->getClientOriginalExtension();

            $file->storeAs('fraud', $filename, 'public');

            $validated['file_pdf'] = $filename;
        }

        Fraud::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = Fraud::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|max:20' . $id,
            'tanggal' => 'required|date',
            'fraud' => 'required',
        ]);

        // $data->update($request->all());

        $data->update($validated);

        return redirect()->back();
    }

    public function delete($id)
    {
        // Fraud::findOrFail($id)->delete();
        $data = Fraud::findOrFail($id);

        if ($data->file_pdf) {
            Storage::disk('public')->delete('fraud/'.$data->file_pdf);
        }

         $data->delete();
         
        return redirect()->back();
    }

    public function getByNik($nik)
    {
        $karyawan = MasterKaryawan::where('nik', $nik)
            ->select('nik','nama','bagian')
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
            ->orWhere('bagian', 'like', "%{$search}%")
            ->select('nik','nama','bagian')
            ->limit(10)
            ->get();

        return response()->json($data);
    }
}
