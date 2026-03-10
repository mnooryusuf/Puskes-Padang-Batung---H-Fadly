<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportController;

Route::get('/pasien/{pasien}/cetak-kartu', [PrintController::class, 'cetakKartuPasien'])->name('pasien.cetak-kartu');
Route::get('/pembayaran/{pembayaran}/cetak-kwitansi', [PrintController::class, 'cetakKwitansi'])->name('pembayaran.cetak-kwitansi');
Route::get('/resep/{resep}/cetak-etiket', [PrintController::class, 'cetakEtiket'])->name('resep.cetak-etiket');

// Routes untuk Laporan
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/lplpo', [ReportController::class, 'lplpo'])->name('lplpo');
    Route::get('/lra', [ReportController::class, 'lra'])->name('lra');
    Route::get('/kunjungan', [ReportController::class, 'kunjungan'])->name('kunjungan');
    Route::get('/kunjungan-poli', [ReportController::class, 'kunjunganPoli'])->name('kunjungan_poli');
    Route::get('/kunjungan-pasien-baru', [ReportController::class, 'kunjunganPasienBaru'])->name('pasien_baru');
    Route::get('/rekap-tindakan', [ReportController::class, 'rekapTindakan'])->name('rekap_tindakan');
    Route::get('/statistik-lab', [ReportController::class, 'statistikLab'])->name('statistik_lab');
    Route::get('/pasien-status', [ReportController::class, 'pasienStatus'])->name('pasien_status');
    Route::get('/obat-expired', [ReportController::class, 'obatExpired'])->name('obat_expired');
    Route::get('/obat-analisa', [ReportController::class, 'obatAnalisa'])->name('obat_analisa');
    Route::get('/pendapatan', [ReportController::class, 'pendapatanHarian'])->name('pendapatan');
    Route::get('/distribusi-penyakit', [ReportController::class, 'distribusiPenyakit'])->name('distribusi_penyakit');
    Route::get('/lb1', [ReportController::class, 'lb1'])->name('lb1');
});
