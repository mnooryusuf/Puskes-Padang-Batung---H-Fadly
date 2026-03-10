<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedisTindakan extends Model
{
    protected $table = 'rekam_medis_tindakan';

    protected $fillable = [
        'rekam_medis_id',
        'tindakan_id',
        'jumlah',
        'harga_snapshot',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function tindakan()
    {
        return $this->belongsTo(Tindakan::class, 'tindakan_id');
    }
}
