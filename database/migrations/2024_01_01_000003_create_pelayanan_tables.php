<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pendaftaran
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->enum('jenis_kunjungan', ['Baru', 'Lama'])->default('Baru');
            $table->foreignId('poli_id')->nullable()->constrained('poli')->nullOnDelete();
            $table->string('jenis_pembayaran')->default('Umum');
            $table->enum('status', [
                'Menunggu Poli', 'Pemeriksaan', 'Menunggu Obat',
                'Menunggu Pembayaran', 'Selesai',
            ])->default('Menunggu Poli');
            $table->date('tanggal_daftar');
            $table->integer('no_antrian');
            $table->timestamps();
        });

        // Tabel Antrian
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->enum('kategori', ['Poli', 'Obat', 'Kasir']);
            $table->string('nomor_antrian');
            $table->enum('status', ['Menunggu', 'Dipanggil', 'Dilayani', 'Selesai', 'Ditolak'])->default('Menunggu');
            $table->foreignId('poli_id')->nullable()->constrained('poli')->nullOnDelete();
            $table->timestamps();
        });

        // Tabel Rekam Medis
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokter');
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->decimal('suhu_tubuh', 4, 1)->nullable();
            $table->integer('nadi')->nullable();
            $table->integer('respirasi')->nullable();
            $table->text('keluhan_utama')->nullable();
            $table->text('riwayat_penyakit_sekarang')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->foreignId('penyakit_id')->nullable()->constrained('penyakit')->nullOnDelete();
            $table->enum('tipe_diagnosis', ['Primer', 'Sekunder'])->default('Primer');
            $table->text('keluhan')->nullable();
            $table->text('diagnosa')->nullable();
            $table->text('tindakan');
            $table->text('instruksi_lab')->nullable();
            $table->string('status_pulang')->nullable();
            $table->string('rs_tujuan')->nullable();
            $table->text('alasan_rujuk')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
        Schema::dropIfExists('antrian');
        Schema::dropIfExists('pendaftaran');
    }
};
