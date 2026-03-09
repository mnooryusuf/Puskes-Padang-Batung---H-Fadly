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
        Schema::table('pasiens', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->after('nama_pasien');
            $table->string('desa_kelurahan')->nullable()->after('alamat');
            $table->string('rt', 5)->nullable()->after('desa_kelurahan');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->enum('cara_bayar', ['Umum', 'BPJS', 'Jamkesda'])->default('Umum')->after('user_id');
            $table->string('no_bpjs')->nullable()->after('cara_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'desa_kelurahan', 'rt', 'rw', 'cara_bayar', 'no_bpjs']);
        });
    }
};
