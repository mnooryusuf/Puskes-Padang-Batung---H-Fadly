<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    use HasFactory;

    protected $fillable = ['nama_obat', 'sediaan', 'kemasan', 'satuan', 'stok', 'stok_minimum', 'expired_at', 'harga_jual'];

    protected $casts = [
        'expired_at' => 'date',
    ];

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    public function detailReseps(): HasMany
    {
        return $this->hasMany(DetailResep::class);
    }
}
