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
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Pemeriksaan Fisik
            $table->decimal('berat_badan', 5, 2)->nullable()->after('dokter_id');
            $table->string('tekanan_darah', 20)->nullable()->after('berat_badan');
            $table->decimal('suhu_tubuh', 4, 1)->nullable()->after('tekanan_darah');
            $table->integer('nadi')->nullable()->after('suhu_tubuh');
            $table->integer('respirasi')->nullable()->after('nadi');

            // Anamnesa detail
            $table->text('keluhan_utama')->nullable()->after('respirasi');
            $table->text('riwayat_penyakit_sekarang')->nullable()->after('keluhan_utama');
            $table->text('riwayat_alergi')->nullable()->after('riwayat_penyakit_sekarang');

            // Diagnosis ICD-10
            $table->foreignId('penyakit_id')->nullable()->after('riwayat_alergi')->constrained('penyakits');
            $table->enum('tipe_diagnosis', ['Primer', 'Sekunder'])->default('Primer')->after('penyakit_id');

            // Instruksi
            $table->text('instruksi_lab')->nullable()->after('tindakan');

            // Status Pulang
            $table->string('status_pulang')->nullable()->after('instruksi_lab'); // Sembuh, Kontrol, Rujuk
            $table->string('rs_tujuan')->nullable()->after('status_pulang');
            $table->text('alasan_rujuk')->nullable()->after('rs_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            //
        });
    }
};
