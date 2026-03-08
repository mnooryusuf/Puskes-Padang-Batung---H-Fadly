<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'obat_id',
        'type',
        'quantity',
        'stock_after',
        'description',
    ];

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
