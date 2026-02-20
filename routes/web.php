<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\AuthController;
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

Route::get('/panel', function () {
    return Inertia::render('dashboard/Panel');
})->middleware(['auth:karyawan', 'admin'])->name('panel');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:karyawan')
    ->name('logout');

Route::get('/dashboard', function () {
    return Inertia::render('dashboard/Dashboard');
})->middleware('auth:karyawan')->name('dashboard');

Route::get('/serahterima', [GeneralController::class, 'serahterima']);