<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use Inertia\Inertia;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    // function index(){
    //     return inertia('auth/Login');
    // }

    function index()
    {
        if (Auth::guard('karyawan')->check()) {

            $user = Auth::guard('karyawan')->user();

            if ($user->flag === 'admin') {
                return redirect('/panel');
            }

            return redirect('/dashboard');
        }

        return inertia('auth/Login');
    }


    function tes(){
        return inertia('Tes');
    }

    function dashboard(){
        return inertia('dashboard/Dashboard');
    }

    function serahterima(){
        return inertia('SerahTerima');
    }
}
