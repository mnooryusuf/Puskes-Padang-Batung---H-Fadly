<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Antrian;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    use HasFactory;

    protected $fillable = ['pasien_id', 'jenis_kunjungan', 'tanggal_daftar', 'poli_id', 'no_antrian', 'jenis_pembayaran', 'status'];

    public static function booted()
    {
        static::created(function (Pendaftaran $pendaftaran) {
            Antrian::create([
                'pendaftaran_id' => $pendaftaran->id,
                'kategori' => 'Poli',
                'nomor_antrian' => $pendaftaran->no_antrian, // Reuse the per-poli number
                'poli_id' => $pendaftaran->poli_id,
                'status' => 'Menunggu',
            ]);
        });
    }

    public static function generateNoAntrian($poliId)
    {
        $today = now()->toDateString();
        $count = static::where('tanggal_daftar', $today)
            ->where('poli_id', $poliId)
            ->count();
        
        return $count + 1;
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function rekamMedis(): HasOne
    {
        return $this->hasOne(RekamMedis::class)->latestOfMany();
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }
}
