<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Obat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'dokter1',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);

        User::create([
            'username' => 'petugas1',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        User::create([
            'username' => 'pasien',
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        // Dokters
        Dokter::create(['nama_dokter' => 'Dr. Ahmad', 'spesialis' => 'Umum']);
        Dokter::create(['nama_dokter' => 'Dr. Siti', 'spesialis' => 'Gigi']);

        // Pasiens
        Pasien::create([
            'no_rm' => 'RM001',
            'nama_pasien' => 'Budiman',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 1',
            'no_hp' => '081234567890',
        ]);

        // Obats
        Obat::create(['nama_obat' => 'Paracetamol', 'satuan' => 'Tablet', 'stok' => 100]);
        Obat::create(['nama_obat' => 'Amoxicillin', 'satuan' => 'Kapsul', 'stok' => 50]);
    }
}
