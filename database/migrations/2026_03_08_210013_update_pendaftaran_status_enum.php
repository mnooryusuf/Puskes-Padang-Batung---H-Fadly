<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convert existing data to match new enum labels
        DB::table('pendaftarans')->where('status', 'Menunggu')->update(['status' => 'Menunggu Poli']);
        DB::table('pendaftarans')->where('status', 'Diperiksa')->update(['status' => 'Pemeriksaan']);
        // 'Selesai' stays 'Selesai'

        // 2. Change column to string first to remove enum constraints
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });
        
        // 3. Re-apply as new Enum
        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('Menunggu Poli', 'Pemeriksaan', 'Menunggu Obat', 'Menunggu Pembayaran', 'Selesai') NOT NULL DEFAULT 'Menunggu Poli'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map back to old labels
        DB::table('pendaftarans')->where('status', 'Menunggu Poli')->update(['status' => 'Menunggu']);
        DB::table('pendaftarans')->where('status', 'Pemeriksaan')->update(['status' => 'Diperiksa']);
        DB::table('pendaftarans')->whereIn('status', ['Menunggu Obat', 'Menunggu Pembayaran'])->update(['status' => 'Menunggu']);

        DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM('Menunggu', 'Diperiksa', 'Selesai') NOT NULL DEFAULT 'Menunggu'");
    }
};
