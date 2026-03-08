<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->enum('kategori', ['Pendaftaran', 'Poli', 'Obat', 'Kasir']);
            $table->string('nomor_antrian');
            $table->enum('status', ['Menunggu', 'Dipanggil', 'Selesai'])->default('Menunggu');
            $table->foreignId('poli_id')->nullable()->constrained('polis')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
