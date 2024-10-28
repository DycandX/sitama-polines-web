<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MahasiswaTa\BimbinganMahasiswaController;
use App\Http\Controllers\API\MahasiswaTa\DashboardMahasiswaController;
use App\Http\Controllers\API\MahasiswaTa\DaftarTaController;
use App\Http\Controllers\API\MahasiswaTa\SidangTaController;
use App\Http\Controllers\API\DosenTa\MahasiswaBimbinganController;
use App\Http\Controllers\API\DosenTa\UjianSidangController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\Auth\AuthController;
use Laravel\Sanctum\Http\Controllers\SanctumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Authentication Login
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Mahasiswa TA
    Route::middleware('auth:sanctum')->group(function () {
        // Home
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Mahasiswa TA
        Route::resource('dashboard-mahasiswa', DashboardMahasiswaController::class);
        // Bimbingan Mahasiswa
        Route::resource('bimbingan-mahasiswa', BimbinganMahasiswaController::class);
        // Daftar TA Mahasiswa
        Route::resource('daftar-tugas-akhir', DaftarTaController::class);
        // Sidang Tugas Akhir
        Route::resource('sidang-tugas-akhir', SidangTaController::class);

        // Dosen TA
        Route::post('/ujian-sidang/kelayakan/{ta_id}', [UjianSidangController::class, 'storeKelayakan'])->name('ujian-sidang.storeKelayakan');

        Route::get('/mhsbimbingan/{ta_id}', [MahasiswaBimbinganController::class, 'pembimbingan'])->name('mhsbimbingan.pembimbingan');
        Route::resource('mhsbimbingan', MahasiswaBimbinganController::class);

        Route::get('/ujian-sidang', [UjianSidangController::class, 'index'])->name('ujian-sidang.index');
    });
});