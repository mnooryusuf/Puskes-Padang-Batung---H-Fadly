<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RekamMedis extends Model
{
    use HasFactory;

    protected $fillable = ['pendaftaran_id', 'dokter_id', 'keluhan', 'diagnosa', 'tindakan'];

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
}
