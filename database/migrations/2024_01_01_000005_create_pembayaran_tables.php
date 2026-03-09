<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->decimal('biaya_konsultasi', 12, 2)->default(0);
            $table->decimal('biaya_obat', 12, 2)->default(0);
            $table->decimal('biaya_tindakan', 12, 2)->default(0);
            $table->decimal('biaya_penunjang', 12, 2)->default(0);
            $table->decimal('biaya_bhp', 12, 2)->default(0);
            $table->decimal('biaya_tambahan', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['Belum Bayar', 'Lunas', 'Gratis'])->default('Belum Bayar');
            $table->string('metode_pembayaran')->nullable();
            $table->string('nomor_kartu_bpjs')->nullable();
            $table->timestamps();
        });

        // Tabel Stock History
        Schema::create('stock_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('stock_after');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_history');
        Schema::dropIfExists('pembayaran');
    }
};
