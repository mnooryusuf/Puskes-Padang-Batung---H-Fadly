<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Resep
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->cascadeOnDelete();
            $table->string('status_pengambilan')->default('Menunggu');
            $table->text('catatan_farmasi')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Resep
        Schema::create('detail_resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obat');
            $table->string('dosis');
            $table->integer('jumlah');
            $table->integer('jumlah_diserahkan')->nullable();
            $table->foreignId('obat_pengganti_id')->nullable()->constrained('obat')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_resep');
        Schema::dropIfExists('resep');
    }
};
