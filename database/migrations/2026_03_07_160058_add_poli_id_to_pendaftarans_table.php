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
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->foreignId('poli_id')->nullable()->after('pasien_id')->constrained('polis')->onDelete('set null');
            $table->dropColumn('poli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('poli')->nullable()->after('tanggal_daftar');
            $table->dropForeign(['poli_id']);
            $table->dropColumn('poli_id');
        });
    }
};
