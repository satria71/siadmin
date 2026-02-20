<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required','numeric'],
            'password' => ['required'],
        ]);

        if (Auth::guard('karyawan')->attempt([
            'nik' => $request->nik,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();

            $user = Auth::guard('karyawan')->user();

            if ($user->flag === 'admin') {
                return redirect()->route('panel');
            } else {
                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'nik' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('karyawan')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');

        dd(Auth::guard('karyawan')->check());
    }
}
