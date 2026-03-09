<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Antrian;

class Resep extends Model
{
    protected $table = 'resep';

    use HasFactory;

    protected $fillable = ['rekam_medis_id', 'status_pengambilan', 'catatan_farmasi'];

    public static function booted()
    {
        static::created(function (Resep $resep) {
            Antrian::create([
                'pendaftaran_id' => $resep->rekamMedis->pendaftaran_id,
                'kategori' => 'Obat',
                'nomor_antrian' => Antrian::generateNomor('Obat'),
                'status' => 'Menunggu',
            ]);
        });
    }

    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function detailReseps(): HasMany
    {
        return $this->hasMany(DetailResep::class);
    }
}
