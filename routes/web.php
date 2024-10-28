<?php

use App\Http\Controllers\DBBackupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Mahasiswa Magang
use App\Http\Controllers\MhsMagang\PendaftaranController;
use App\Http\Controllers\MhsMagang\JadwalSeminarController;
use App\Http\Controllers\MhsMagang\LaporanController;
use App\Http\Controllers\MhsMagang\LogbookMhsController;
use App\Http\Controllers\MhsMagang\JadwalBimbinganController;
use App\Http\Controllers\MhsMagang\NilaimhsController;

// Dosen dan Admin Magang 
use App\Http\Controllers\DosenMagang\DataBimbinganController;
use App\Http\Controllers\DosenMagang\nilaiController;
use App\Http\Controllers\AdminMagang\SeminarController;
use App\Http\Controllers\AdminMagang\MagangController;
use App\Http\Controllers\AdminMagang\DasboardController;

// Admin TA
use App\Http\Controllers\AdminTa\BimbinganController;
use App\Http\Controllers\AdminTa\TaController;

use App\Http\Controllers\Auth\GoogleLoginController;
// Dosen TA
use App\Http\Controllers\DosenTa\MahasiswaBimbinganController;
use App\Http\Controllers\DosenTa\UjianSidangController;

// Mahasiswa TA
use App\Http\Controllers\MahasiswaTa\BimbinganMahasiswaController;
use App\Http\Controllers\MahasiswaTa\DaftarTaController;
use App\Http\Controllers\MahasiswaTa\DashboardMahasiswaController;
use App\Http\Controllers\MahasiswaTa\SidangTaController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::permanentRedirect('/', '/login');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::resource('profil', ProfilController::class)->except('destroy');

Route::get('reset-password-request', [HomeController::class, 'resetPasswordFormRequest'])->name('reset-password-request');
Route::post('reset-password-request', [HomeController::class, 'resetPasswordFormRequestAction'])->name('reset-password-request-action');

Route::get('reset-password', [HomeController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password', [HomeController::class, 'resetPasswordAction'])->name('reset-password-action');

Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

// use App\Http\Controllers\Auth\GoogleSocialiteController;

// Route::get('auth/google', [GoogleSocialiteController::class, 'redirectToGoogle']);  // redirect to google login
// Route::get('callback/google', [GoogleSocialiteController::class, 'handleCallback']);    // callback route after google account chosen


Route::middleware(['auth'])->group(function () {
    Route::resource('manage-user', UserController::class);
    Route::resource('manage-role', RoleController::class);
    Route::resource('manage-menu', MenuController::class);
    Route::resource('manage-permission', PermissionController::class)->only('store', 'destroy');

    Route::get('stream-document/{enc_path}', [HomeController::class, 'streamDocument']);

    // Route Mahasiswa Magang
    Route::resource('nilai-magang', NilaimhsController::class);
    Route::resource('seminar-magang', JadwalSeminarController::class);
    Route::resource('logbook-magang', LogbookMhsController::class);
    Route::resource('jadwal-bimbingan', JadwalBimbinganController::class);
    Route::resource('laporanmagang', LaporanController::class);
    Route::resource('daftar-magang', PendaftaranController::class);
    Route::post('daftar-magang/storeTambah', [PendaftaranController::class, 'storeTambah'])->name('daftar-magang.storeTambah');
    Route::get('/daftar-magang', [PendaftaranController::class, 'index'])->name('daftar-magang.index');
    Route::get('/daftar-magang/dataindustri', [PendaftaranController::class, 'dataindustri'])->name('daftar-magang.dataindustri');
    Route::get('/daftar-magang/upload', [PendaftaranController::class, 'upload'])->name('daftar-magang.upload');

    // Route Dosen Magang 
    Route::get('/bimbingan-dosen-magang', [DataBimbinganController::class, 'index'])->name('bimbingan-dosen-magang.index');
    Route::get('/bimbingan-dosen-magang/contoh', [DataBimbinganController::class, 'contoh'])->name('bimbingan-dosen-magang.contoh');
    Route::get('/bimbingan-dosen-magang/logbook/{magang_id}', [DataBimbinganController::class, 'logbook'])->name('bimbingan-dosen-magang.logbook');
    Route::get('/bimbingan/laporan/{magang_id}', [DataBimbinganController::class, 'laporan'])->name('bimbingan-dosen-magang.laporan');
    Route::get('/bimbingan/bimbingan/{magang_id}', [DataBimbinganController::class, 'bimbingan'])->name('bimbingan-dosen-magang.bimbingan');
    Route::get('/bimbingan/validasi/{magang_id}', [DataBimbinganController::class, 'validasi'])->name('bimbingan-dosen-magang.validasi');
    Route::post('/bimbingan/valid', [DataBimbinganController::class, 'valid'])->name('bimbingan.valid');
    Route::post('/bimbingan-dosen-magang/verify', [DataBimbinganController::class, 'verify'])->name('bimbingan.verify');
    Route::get('/nilai-dosen-magang/nilaidosen/{magang_industri_id}', [nilaiController::class, 'nilaidosen'])->name('nilai-dosen-magang.nilaidosen');
    Route::post('nilai-dosen-magang/store', [nilaiController::class, 'store'])->name('nilai.store');
    Route::get('/nilai-dosen-magang/nilaiindustri/{magang_id}', [nilaiController::class, 'nilaiindustri'])->name('nilai-dosen-magang.nilaiindustri');
    Route::post('nilai-dosen-magang/update', [nilaiController::class, 'update'])->name('nilai.update');
    Route::resource('nilai-dosen-magang', nilaiController::class);

    // Route Admin Magang
    Route::resource('grafik', DasboardController::class);
    Route::resource('data-magang', MagangController::class);
    Route::get('/data-magang/create', [MagangController::class, 'create'])->name('magang.create');
    Route::post('data-magang/store', [MagangController::class, 'store'])->name('magang.store');
    Route::get('/data-magang/edit/{magang_id}', [MagangController::class, 'edit'])->name('magang.edit');
    Route::post('/data-magang/editDosen', [MagangController::class, 'editDosen'])->name('magang.editDosen');
    Route::get('/data-magang/syarat/{magang_id}', [MagangController::class, 'syarat'])->name('magang.syarat');
    Route::get('/data-magang/nilai/{magang_id}', [MagangController::class, 'nilai'])->name('magang.nilai');
    Route::post('/data-magang', [MagangController::class, 'index'])->name('magang.index');
    Route::resource('seminar', SeminarController::class);
    Route::get('/seminar/create', [SeminarController::class, 'create'])->name('seminar.create');
    Route::post('/seminar/store', [SeminarController::class, 'store'])->name('seminar.store');
    Route::get('/seminar/edit/{magang_id}', [SeminarController::class, 'show'])->name('seminar.edit');
    Route::post('/seminar/ubah', [SeminarController::class, 'ubah'])->name('seminar.ubah');
    Route::post('/seminar/valid', [SeminarController::class, 'valid'])->name('seminar.valid');


    // Admin TA
    Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan');
    Route::get('/bimbingan/{bimbingan}/bimblog', [BimbinganController::class, 'bimblog'])->name('bimbingan.bimblog');
    Route::post('/bimbingan/{bimbingan}/verifyAll', [BimbinganController::class, 'verifyAll'])->name('bimbinganTa.verifyAll');
    Route::get('/syarat-sidang-verifikasi-single/{syarat_sidang_id}', [BimbinganController::class, 'verifySingle'])->name('bimbinganTa.verifySingle');
    Route::resource('bimbingan', BimbinganController::class);

    Route::get('/ta', [TaController::class, 'index'])->name('ta');
    Route::get('satistik-penguji', [TaController::class, 'statistikPenguji'])->name('ta.statistikPenguji');
    Route::get('/ta/{taSidangId}/editPenguji', [TaController::class, 'editPenguji'])->name('ta.editPenguji');
    Route::post('/ta/{taSidangId}/updatePenguji', [TaController::class, 'updatePenguji'])->name('ta.updatePenguji');
    Route::post('/ta/{taSidangId}/updateOrInsertStatusLulus', [TaController::class, 'updateOrInsertStatusLulus'])->name('updateOrInsertStatusLulus');
    Route::resource('ta', TaController::class);
    Route::get('ta/cetak_surat_tugas_admin/{id}', [TaController::class, 'CetakSuratTugasAdmin'])->name('ta.CetakSuratTugasAdmin');

    // Mahasiswa TA
    Route::get('dashboard-mahasiswa/autocomplete', [DashboardMahasiswaController::class, 'autocomplete']);
    Route::resource('dashboard-mahasiswa', DashboardMahasiswaController::class);

    Route::get('bimbingan-mahasiswa/cetak-persetujuan-sidang', [BimbinganMahasiswaController::class, 'cetak_persetujuan_sidang'])->name('bimbingan-mahasiswa.cetak_persetujuan_sidang');
    Route::get('bimbingan-mahasiswa/cetak_lembar_kontrol/{id}/{sebagai}', [BimbinganMahasiswaController::class, 'CetakLembarKontrol'])->name('bimbingan-mahasiswa.CetakLembarKontrol');
    Route::resource('bimbingan-mahasiswa', BimbinganMahasiswaController::class);

    Route::get('daftar-tugas-akhir/{daftar_tugas_akhir}/upload', [DaftarTaController::class, 'upload'])->name('daftar-tugas-akhir.upload');
    Route::post('daftar-tugas-akhir/daftar', [DaftarTaController::class, 'daftar'])->name('daftar-tugas-akhir.daftar');
    Route::post('daftar-tugas-akhir/upload', [DaftarTaController::class, 'uploadSingle'])->name('daftar-tugas-akhir.uploadSingle');
    Route::resource('daftar-tugas-akhir', DaftarTaController::class);

    Route::get('sidang-tugas-akhir/surat-tugas', [SidangTaController::class, 'suratTugas']);
    Route::resource('sidang-tugas-akhir', SidangTaController::class);
    Route::get('upload-lembar-pengesahan', [SidangTaController::class, 'upload_lembar_pengesahan']);
    Route::post('sidang-tugas-akhir/upload-lembar', [SidangTaController::class, 'upload_lembar']);

    // Dosen TA
    Route::get('/ujian-sidang', [UjianSidangController::class, 'index'])->name('ujian-sidang.index');
    Route::get('ujian-sidang/kelayakan/{ta_id}', [UjianSidangController::class, 'kelayakan'])->name('ujian-sidang.kelayakan');
    Route::get('ujian-sidang/penguji/{ta_id}', [UjianSidangController::class, 'penguji'])->name('ujian-sidang.penguji');
    Route::get('ujian-sidang/revisi/{ta_id}', [UjianSidangController::class, 'showRevisi'])->name('ujian-sidang.revisi');
    Route::get('ujian-sidang/revisi2/{ta_id}', [UjianSidangController::class, 'showRevisi2'])->name('ujian-sidang.revisi2');
    Route::post('/ujian-sidang/kelayakan/{ta_id}', [UjianSidangController::class, 'storeKelayakan'])->name('ujian-sidang.storeKelayakan');
    Route::post('/ujian-sidang/penguji/{ta_id}', [UjianSidangController::class, 'storePenguji'])->name('ujian-sidang.storePenguji');
    Route::get('ujian-sidang/cetak_surat_tugas/{id}', [UjianSidangController::class, 'CetakSuratTugas'])->name('ujian-sidang.CetakSuratTugas');
    Route::get('ujian-sidang/cetak_rekap_nilai/{id}', [UjianSidangController::class, 'CetakRekapNilai'])->name('ujian-sidang.CetakRekapNilai');
    Route::get('ujian-sidang/nilai-pembimbing/{ta_sidang_id}', [UjianSidangController::class, 'nilaiPembimbing'])->name('ujian-sidang.nilai-pembimbing');
    Route::get('ujian-sidang/nilai-penguji/{ta_sidang_id}', [UjianSidangController::class, 'nilaiPenguji'])->name('ujian-sidang.nilai-penguji');
    Route::get('ujian-sidang/berita-acara/{ta_sidang_id}', [UjianSidangController::class, 'beritaAcara'])->name('ujian-sidang.berita-acara');


    Route::get('/mhsbimbingan', [MahasiswaBimbinganController::class, 'index'])->name('mhsbimbingan.index');
    Route::post('/setujui-sidang-akhir/{ta_id}', [MahasiswaBimbinganController::class, 'setujuiSidangAkhir'])->name('setujui.sidang.akhir');
    Route::post('/setujui-pembimbingan/{ta_id}', [MahasiswaBimbinganController::class, 'setujuiPembimbingan'])->name('setujui-pembimbingan');
    Route::get('/mhsbimbingan/{ta_id}', [MahasiswaBimbinganController::class, 'pembimbingan'])->name('mhsbimbingan.pembimbingan');
    Route::resource('mhsbimbingan', MahasiswaBimbinganController::class);

    Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);
});
