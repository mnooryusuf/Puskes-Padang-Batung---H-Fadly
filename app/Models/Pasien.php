<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = ['no_rm', 'nama_pasien', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_hp', 'user_id'];

    public static function booted()
    {
        static::created(function (Pasien $pasien) {
            if (!$pasien->user_id) {
                $user = User::create([
                    'username' => $pasien->no_rm,
                    'password' => \Illuminate\Support\Facades\Hash::make('Puskes' . $pasien->no_rm),
                    'role' => 'pasien',
                ]);

                $pasien->user_id = $user->id;
                $pasien->saveQuietly();
            }
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
