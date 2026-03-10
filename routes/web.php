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
    Route::get('/lb1', [ReportController::class, 'lb1'])->name('lb1');
});

