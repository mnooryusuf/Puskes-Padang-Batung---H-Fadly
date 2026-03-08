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
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->decimal('biaya_penunjang', 15, 2)->default(0)->after('biaya_tindakan');
            $table->decimal('biaya_bhp', 15, 2)->default(0)->after('biaya_penunjang');
            $table->string('nomor_kartu_bpjs')->nullable()->after('metode_pembayaran');
        });

        // Update enum for status_pembayaran
        DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status_pembayaran ENUM('Belum Lunas', 'Lunas', 'Piutang', 'Gratis') NOT NULL DEFAULT 'Belum Lunas'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['biaya_penunjang', 'biaya_bhp', 'nomor_kartu_bpjs']);
        });

        DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status_pembayaran ENUM('Belum Lunas', 'Lunas') NOT NULL DEFAULT 'Belum Lunas'");
    }
};
