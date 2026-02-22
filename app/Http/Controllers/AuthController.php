<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;


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

    public function prosesregister(Request $request)
    {
        // Validasi
        $request->validate([
            'nik' => 'required|unique:karyawans,nik',
            'nama' => 'required|string|max:255',
            'bagian' => 'required|string|max:255',
            'password' => 'required|min:3',
        ]);

        // Simpan ke database
        Karyawan::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'bagian' => $request->bagian,
            'password' => Hash::make($request->password),
        ]);

        // Redirect setelah sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil!');
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
