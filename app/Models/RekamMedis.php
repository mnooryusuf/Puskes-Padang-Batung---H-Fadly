<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';

    use HasFactory;

    protected $fillable = [
        'pendaftaran_id', 
        'dokter_id', 
        'berat_badan', 
        'tekanan_darah', 
        'suhu_tubuh', 
        'nadi', 
        'respirasi', 
        'keluhan_utama', 
        'riwayat_penyakit_sekarang', 
        'riwayat_alergi', 
        'penyakit_id', 
        'tipe_diagnosis', 
        'keluhan', 
        'diagnosa', 
        'tindakan', 
        'instruksi_lab', 
        'status_pulang', 
        'rs_tujuan', 
        'alasan_rujuk'
    ];

    public function penyakit(): BelongsTo
    {
        return $this->belongsTo(Penyakit::class);
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function resep(): HasOne
    {
        return $this->hasOne(Resep::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->pendaftaran->pasien();
    }
}
