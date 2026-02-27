<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Fraud;

use Illuminate\Http\Request;

class FraudController extends Controller
{
    public function index()
    {
        return Inertia::render('fraud/Fraud');
    }

    public function data(Request $request)
    {
        $columns = ['id', 'tanggal', 'nik', 'fraud'];

        $query = Fraud::query();

        // Search
    if ($request->filled('search.value')) {
        $search = $request->input('search.value');

        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhere('tanggal', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('fraud', 'like', "%{$search}%");
        });
    }

        $total = $query->count();

        // Order
        if ($request->order) {
            $query->orderBy(
                $columns[$request->order[0]['column']],
                $request->order[0]['dir']
            );
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
}
