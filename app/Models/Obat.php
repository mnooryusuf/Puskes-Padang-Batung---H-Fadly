<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    use HasFactory;

    protected $fillable = ['nama_obat', 'satuan', 'stok', 'harga_jual'];

    public function detailReseps(): HasMany
    {
        return $this->hasMany(DetailResep::class);
    }
}
