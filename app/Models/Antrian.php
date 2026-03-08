<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $fillable = [
        'pendaftaran_id',
        'kategori',
        'nomor_antrian',
        'status',
        'poli_id',
    ];

    public static function booted()
    {
        static::updated(function (Antrian $antrian) {
            // Jika antrian Obat selesai, otomatis buat antrian Kasir
            if ($antrian->kategori === 'Obat' && $antrian->status === 'Selesai' && $antrian->getOriginal('status') !== 'Selesai') {
                static::create([
                    'pendaftaran_id' => $antrian->pendaftaran_id,
                    'kategori' => 'Kasir',
                    'nomor_antrian' => static::generateNomor('Kasir'),
                    'status' => 'Menunggu',
                ]);
            }
        });
    }

    public static function generateNomor($kategori, $poliId = null)
    {
        $today = now()->toDateString();
        $query = static::whereDate('created_at', $today)
            ->where('kategori', $kategori);

        if ($poliId) {
            $query->where('poli_id', $poliId);
        }

        $count = $query->count();
        $prefix = match ($kategori) {
            'Pendaftaran' => 'REG-',
            'Poli' => 'P-',
            'Obat' => 'A-', // Apotek
            'Kasir' => 'K-',
            default => '',
        };

        return $prefix . ($count + 1);
    }

    public function pendaftaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function poli(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }
}
