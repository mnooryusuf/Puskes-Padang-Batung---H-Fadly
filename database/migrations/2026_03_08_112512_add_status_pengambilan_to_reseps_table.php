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
        Schema::table('reseps', function (Blueprint $table) {
            $table->string('status_pengambilan')->default('Menunggu')->after('rekam_medis_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            //
        });
    }
};
