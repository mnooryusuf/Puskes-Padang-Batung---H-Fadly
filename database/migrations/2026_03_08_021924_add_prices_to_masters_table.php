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
        Schema::table('obats', function (Blueprint $table) {
            $table->decimal('harga_jual', 15, 2)->default(0)->after('stok');
        });

        Schema::table('polis', function (Blueprint $table) {
            $table->decimal('biaya_konsultasi', 15, 2)->default(0)->after('nama_poli');
            $table->decimal('biaya_registrasi', 15, 2)->default(0)->after('biaya_konsultasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });

        Schema::table('polis', function (Blueprint $table) {
            $table->dropColumn(['biaya_konsultasi', 'biaya_registrasi']);
        });
    }
};
