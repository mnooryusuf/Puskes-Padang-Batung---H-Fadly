<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\PrintController;

Route::get('/pasien/{pasien}/cetak-kartu', [PrintController::class, 'cetakKartuPasien'])->name('pasien.cetak-kartu');
Route::get('/pembayaran/{pembayaran}/cetak-kwitansi', [PrintController::class, 'cetakKwitansi'])->name('pembayaran.cetak-kwitansi');

