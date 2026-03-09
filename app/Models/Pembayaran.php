<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Antrian;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'biaya_pendaftaran',
        'biaya_konsultasi',
        'biaya_obat',
        'biaya_tindakan',
        'biaya_penunjang',
        'biaya_bhp',
        'biaya_tambahan',
        'total_bayar',
        'status_pembayaran',
        'metode_pembayaran',
        'nomor_kartu_bpjs',
    ];

    public static function booted()
    {
        static::updated(function (Pembayaran $pembayaran) {
            $finalStatuses = ['Lunas', 'Piutang', 'Gratis'];
            $newStatus = $pembayaran->status_pembayaran;
            $oldStatus = $pembayaran->getOriginal('status_pembayaran');

            if (in_array($newStatus, $finalStatuses) && !in_array($oldStatus, $finalStatuses)) {
                Antrian::where('pendaftaran_id', $pembayaran->pendaftaran_id)
                    ->where('kategori', 'Kasir')
                    ->update(['status' => 'Selesai']);
                
                $pembayaran->pendaftaran->update(['status' => 'Selesai']);
            }
        });

        static::created(function (Pembayaran $pembayaran) {
            $finalStatuses = ['Lunas', 'Piutang', 'Gratis'];
            if (in_array($pembayaran->status_pembayaran, $finalStatuses)) {
                Antrian::where('pendaftaran_id', $pembayaran->pendaftaran_id)
                    ->where('kategori', 'Kasir')
                    ->update(['status' => 'Selesai']);
                
                $pembayaran->pendaftaran->update(['status' => 'Selesai']);
            }
        });
    }

    public function pendaftaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
