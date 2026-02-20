<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    function index(){
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
