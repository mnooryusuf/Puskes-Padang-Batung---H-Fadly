<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailResep extends Model
{
    use HasFactory;

    protected $fillable = ['resep_id', 'obat_id', 'dosis', 'jumlah', 'jumlah_diserahkan', 'obat_pengganti_id'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    /**
     * Obat pengganti (substitusi) dari apoteker.
     */
    public function obatPengganti(): BelongsTo
    {
        return $this->belongsTo(Obat::class, 'obat_pengganti_id');
    }

    /**
     * Get the actual drug to dispense (substituted or original).
     */
    public function getObatAktualAttribute(): ?Obat
    {
        return $this->obat_pengganti_id ? $this->obatPengganti : $this->obat;
    }

    /**
     * Get the actual quantity to dispense.
     */
    public function getJumlahAktualAttribute(): int
    {
        return $this->jumlah_diserahkan ?? $this->jumlah;
    }
}
