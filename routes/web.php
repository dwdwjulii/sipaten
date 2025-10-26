<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\TahapController;
use App\Http\Controllers\PencatatanController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // ==========================================================
    // PROFIL (SEMUA USER)
    // ==========================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================================
    // PENCATATAN
    // ==========================================================
    // Custom: Buat catatan berdasarkan anggota tertentu
    Route::get('/pencatatan/{anggota}/create', [PencatatanController::class, 'create'])
        ->name('pencatatan.create')
        ->middleware('petugas');

    Route::post('/pencatatan/store', [PencatatanController::class, 'store'])
        ->name('pencatatan.store')
        ->middleware('petugas');

    // Mulai pencatatan baru (khusus admin)
    Route::get('/pencatatan/mulai-baru', [PencatatanController::class, 'mulaiBaru'])
        ->name('pencatatan.mulaiBaru')
        ->middleware('admin');

    // Resource utama (tanpa create, karena sudah custom di atas)
    Route::resource('pencatatan', PencatatanController::class)->except(['create']);

    // ==========================================================
    // HALAMAN ADMIN
    // ==========================================================
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chartData');

        Route::resource('pengguna', UserController::class)
            ->parameters(['pengguna' => 'user']) 
            ->except(['create', 'edit', 'show']);
        Route::resource('anggota', AnggotaController::class)->except(['create', 'edit', 'show']);
        Route::resource('tahap', TahapController::class)->only(['store', 'destroy', 'update']);
        Route::get('/tahap/{id}/check', [TahapController::class, 'check']);

        
        // Laporan & Arsip
        Route::get('/laporan/tahap/export', [PencatatanController::class, 'exportLaporanTahap'])->name('laporan.tahap.export');
        Route::post('/laporan/arsip-keseluruhan', [PencatatanController::class, 'archiveLaporanKeseluruhan'])->name('laporan.arsip.keseluruhan');
        Route::get('/laporan/export/keseluruhan', [PencatatanController::class, 'exportLaporanKeseluruhan'])->name('laporan.keseluruhan.export');

        Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
        Route::get('/arsip/{arsip}', [ArsipController::class, 'show'])->name('arsip.show');
        Route::get('/arsip/tahun/{tahun}', [ArsipController::class, 'byYear'])->name('arsip.tahun');
        Route::post('/arsip/validasi/{tahun}', [ArsipController::class, 'validasi'])->name('arsip.validasi');
        Route::delete('/arsip/{arsip}', [ArsipController::class, 'destroy'])->name('arsip.destroy');

        // Reset pencatatan
        Route::post('/pencatatan/reset', [PencatatanController::class, 'reset'])->name('pencatatan.reset');
    });
});

require __DIR__.'/auth.php';
