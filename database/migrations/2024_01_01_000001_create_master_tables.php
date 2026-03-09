<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Poli
        Schema::create('poli', function (Blueprint $table) {
            $table->id();
            $table->string('nama_poli')->unique();
            $table->decimal('biaya_registrasi', 12, 2)->default(0);
            $table->decimal('biaya_konsultasi', 12, 2)->default(0);
            $table->timestamps();
        });

        // Tabel Penyakit (ICD-10)
        Schema::create('penyakit', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_penyakit');
            $table->timestamps();
        });

        // Tabel Obat
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->string('sediaan')->nullable();
            $table->string('kemasan')->nullable();
            $table->string('satuan');
            $table->integer('stok')->default(0);
            $table->date('expired_at')->nullable();
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
        Schema::dropIfExists('penyakit');
        Schema::dropIfExists('poli');
    }
};
