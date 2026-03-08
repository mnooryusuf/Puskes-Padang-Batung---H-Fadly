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
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->decimal('biaya_pendaftaran', 15, 2)->default(0)->after('pendaftaran_id');
            $table->decimal('biaya_konsultasi', 15, 2)->default(0)->after('biaya_pendaftaran');
            $table->decimal('biaya_obat', 15, 2)->default(0)->after('biaya_konsultasi');
            $table->decimal('biaya_tindakan', 15, 2)->default(0)->after('biaya_obat');
            $table->decimal('biaya_tambahan', 15, 2)->default(0)->after('biaya_tindakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn([
                'biaya_pendaftaran',
                'biaya_konsultasi',
                'biaya_obat',
                'biaya_tindakan',
                'biaya_tambahan'
            ]);
        });
    }
};
