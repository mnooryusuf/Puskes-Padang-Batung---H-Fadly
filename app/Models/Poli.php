<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $fillable = ['nama_poli'];

    public function dokters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dokter::class);
    }

    public function pendaftarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
