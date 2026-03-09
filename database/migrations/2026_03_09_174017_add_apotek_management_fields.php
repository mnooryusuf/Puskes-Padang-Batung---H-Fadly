<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add catatan_farmasi to reseps
        Schema::table('reseps', function (Blueprint $table) {
            $table->text('catatan_farmasi')->nullable()->after('status_pengambilan');
        });

        // Add jumlah_diserahkan and obat_pengganti_id to detail_reseps
        Schema::table('detail_reseps', function (Blueprint $table) {
            $table->integer('jumlah_diserahkan')->nullable()->after('jumlah');
            $table->foreignId('obat_pengganti_id')->nullable()->after('jumlah_diserahkan')
                ->constrained('obats')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_reseps', function (Blueprint $table) {
            $table->dropForeign(['obat_pengganti_id']);
            $table->dropColumn(['jumlah_diserahkan', 'obat_pengganti_id']);
        });

        Schema::table('reseps', function (Blueprint $table) {
            $table->dropColumn('catatan_farmasi');
        });
    }
};
