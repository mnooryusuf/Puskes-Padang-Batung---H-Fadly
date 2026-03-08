<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = ['no_rm', 'nik', 'nama_pasien', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_hp', 'user_id'];

    public static function generateNoRm(): string
    {
        $prefix = 'RM-' . date('Ym') . '-';
        $lastRecord = static::where('no_rm', 'like', $prefix . '%')
            ->orderBy('no_rm', 'desc')
            ->first();

        if (!$lastRecord) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRecord->no_rm);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

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

    public function rekamMedis(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(RekamMedis::class, Pendaftaran::class);
    }
}
