<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    use HasFactory;

    protected $fillable = ['nama_dokter', 'spesialis', 'user_id'];

    public static function booted()
    {
        static::created(function (Dokter $dokter) {
            if (!$dokter->user_id) {
                $username = strtolower(str_replace(' ', '', $dokter->nama_dokter));
                
                $user = User::create([
                    'username' => $username,
                    'password' => \Illuminate\Support\Facades\Hash::make('Dokter123'),
                    'role' => 'dokter',
                ]);

                $dokter->user_id = $user->id;
                $dokter->saveQuietly();
            }
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function poli(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class);
    }

    public function jadwalDokters(): HasMany
    {
        return $this->hasMany(JadwalDokter::class);
    }
}
