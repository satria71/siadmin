<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FraudController;
use App\Http\Controllers\SerahTerimaController;
use App\Http\Controllers\MasterKaryawanController;
use App\Http\Controllers\AbsensiController;

use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [GeneralController::class, 'index']);

Route::post('/login', [AuthController::class, 'proseslogin'])
    ->name('login');

Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');


Route::middleware('auth:karyawan')->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('dashboard/Dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});

Route::middleware(['auth:karyawan', 'admin'])->group(function () {

    Route::get('/panel', function () {
        return Inertia::render('dashboard/Panel');
    })->name('panel');

    // Route::get('/serahterima', function () {
    //     return Inertia::render('SerahTerima');
    // })->name('serahterima');

    Route::get('/serahterima', [SerahTerimaController::class, 'index'])->name('serahterima');

    // Route::get('/register', function () {
    //     return Inertia::render('auth/Register');
    // })->name('register');

    //Register
    Route::get('/register', [AuthController::class, 'viewregister'])->name('register');
    Route::post('/register', [AuthController::class, 'prosesregister']);

    //Fraud
    Route::get('/fraud', [FraudController::class, 'index'])->name('fraud');
    Route::get('/fraud/data', [FraudController::class, 'data'])->name('fraud.data');
    Route::post('/fraud/store', [FraudController::class, 'store']);
    Route::post('/fraud/update/{id}', [FraudController::class, 'update']);
    Route::delete('/fraud/delete/{id}', [FraudController::class, 'delete']);
    Route::get('/fraud/by-nik/{nik}', [FraudController::class, 'getByNik']);
    Route::get('/fraud/search', [FraudController::class, 'search']);

    //Master Karyawan
    Route::get('/masterKaryawan', [MasterKaryawanController::class, 'index'])->name('masterKaryawan');
    Route::get('/masterKaryawan/data', [MasterKaryawanController::class, 'data'])->name('masterKaryawan.data');
    Route::get('/masterKaryawan/{id}', [MasterKaryawanController::class, 'tampilDetailKaryawan'])->name('masterKaryawan');
    Route::post('/masterKaryawan/store', [MasterKaryawanController::class, 'store']);
    Route::put('/masterKaryawan/update/{id}', [MasterKaryawanController::class, 'update']);
    Route::delete('/masterKaryawan/delete/{id}', [MasterKaryawanController::class, 'delete']);

    //Absensi Admin
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi/upload', [AbsensiController::class, 'upload'])->name('absensi.upload');
    Route::get('/absensi/data', [AbsensiController::class, 'data']);
    Route::post('/absensi/finalisasi',[AbsensiController::class,'finalisasi']);
    Route::get('/absensi/detail/{id}', [AbsensiController::class,'detail']);
});