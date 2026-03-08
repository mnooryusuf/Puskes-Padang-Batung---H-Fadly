<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Antrian;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'biaya_pendaftaran',
        'biaya_konsultasi',
        'biaya_obat',
        'biaya_tindakan',
        'biaya_tambahan',
        'total_bayar',
        'status_pembayaran',
        'metode_pembayaran',
    ];

    public static function booted()
    {
        static::updated(function (Pembayaran $pembayaran) {
            if ($pembayaran->status_pembayaran === 'Lunas' && $pembayaran->getOriginal('status_pembayaran') !== 'Lunas') {
                Antrian::where('pendaftaran_id', $pembayaran->pendaftaran_id)
                    ->where('kategori', 'Kasir')
                    ->update(['status' => 'Selesai']);
            }
        });

        static::created(function (Pembayaran $pembayaran) {
            if ($pembayaran->status_pembayaran === 'Lunas') {
                Antrian::where('pendaftaran_id', $pembayaran->pendaftaran_id)
                    ->where('kategori', 'Kasir')
                    ->update(['status' => 'Selesai']);
            }
        });
    }

    public function pendaftaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
