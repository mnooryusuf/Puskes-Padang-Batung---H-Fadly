<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tindakan extends Model
{
    protected $table = 'tindakan';

    protected $fillable = [
        'nama_tindakan',
        'kategori',
        'harga',
        'is_active',
    ];

    public function rekamMedis(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RekamMedis::class, 'rekam_medis_tindakan')
                    ->withPivot('jumlah', 'harga_snapshot')
                    ->withTimestamps();
    }

    public function rekamMedisTindakans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RekamMedisTindakan::class, 'tindakan_id');
    }
}
