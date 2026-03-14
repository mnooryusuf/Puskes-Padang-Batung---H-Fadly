<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pasien
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('user')->nullOnDelete();
            $table->enum('cara_bayar', ['Umum', 'BPJS'])->default('Umum');
            $table->string('no_bpjs')->nullable();
            $table->string('no_rm')->unique();
            $table->string('nik')->nullable();
            $table->string('nama_pasien');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('desa_kelurahan')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('no_hp');
            $table->string('status_hidup')->default('Hidup');
            $table->timestamps();
        });

        // Tabel Dokter
        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->nullable()->constrained('poli')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('user')->nullOnDelete();
            $table->string('nama_dokter');
            $table->string('spesialis');
            $table->timestamps();
        });

        // Tabel Jadwal Dokter
        Schema::create('jadwal_dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokter')->cascadeOnDelete();
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokter');
        Schema::dropIfExists('dokter');
        Schema::dropIfExists('pasien');
    }
};
