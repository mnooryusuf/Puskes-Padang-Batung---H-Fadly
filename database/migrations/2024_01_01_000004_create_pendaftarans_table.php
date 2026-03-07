<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->date('tanggal_daftar');
            $table->string('poli', 50);
            $table->integer('no_antrian');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pendaftarans'); }
};
