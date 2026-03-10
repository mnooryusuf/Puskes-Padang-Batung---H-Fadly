<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Poli;
use App\Models\Penyakit;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\JadwalDokter;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. USERS
        // =============================================
        $admin = User::create([
            'username' => 'admin',
            'nama_lengkap' => 'Administrator Puskesmas',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        $petugas = User::create([
            'username' => 'petugas',
            'nama_lengkap' => 'Petugas Pendaftaran',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
        ]);
        $userDokter1 = User::create([
            'username' => 'dr.ahmad',
            'nama_lengkap' => 'dr. Ahmad Fadillah',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
        ]);
        $userDokter2 = User::create([
            'username' => 'dr.siti',
            'nama_lengkap' => 'drg. Siti Nurhaliza',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
        ]);
        $userDokter3 = User::create([
            'username' => 'dr.lina',
            'nama_lengkap' => 'dr. Lina Marlina, Sp.A',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
        ]);
        $userDokter4 = User::create([
            'username' => 'dr.richard',
            'nama_lengkap' => 'dr. Richard Hartono, Sp.S',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
        ]);
        $apoteker = User::create([
            'username' => 'apoteker',
            'nama_lengkap' => 'Apt. Rahma Dewi',
            'password' => Hash::make('apoteker123'),
            'role' => 'apoteker',
        ]);
        $kasir = User::create([
            'username' => 'kasir',
            'nama_lengkap' => 'Sari Mulyani',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);
        $kepala = User::create([
            'username' => 'kepala',
            'nama_lengkap' => 'dr. H. Fadly',
            'password' => Hash::make('kepala123'),
            'role' => 'kepala',
        ]);

        // =============================================
        // 2. POLI
        // =============================================
        $poliUmum = Poli::create([
            'nama_poli' => 'Poli Umum',
            'biaya_registrasi' => 10000,
            'biaya_konsultasi' => 25000,
        ]);
        $poliGigi = Poli::create([
            'nama_poli' => 'Poli Gigi',
            'biaya_registrasi' => 10000,
            'biaya_konsultasi' => 35000,
        ]);
        $poliAnak = Poli::create([
            'nama_poli' => 'Poli Anak',
            'biaya_registrasi' => 10000,
            'biaya_konsultasi' => 30000,
        ]);
        $poliSaraf = Poli::create([
            'nama_poli' => 'Poli Saraf',
            'biaya_registrasi' => 10000,
            'biaya_konsultasi' => 40000,
        ]);
        $poliKIA = Poli::create([
            'nama_poli' => 'Poli KIA',
            'biaya_registrasi' => 10000,
            'biaya_konsultasi' => 25000,
        ]);

        // =============================================
        // 3. DOKTER (linked to User & Poli)
        // =============================================
        $dokter1 = Dokter::create([
            'user_id' => $userDokter1->id,
            'poli_id' => $poliUmum->id,
            'nama_dokter' => 'dr. Ahmad Fadillah',
            'spesialis' => 'Umum',
        ]);
        $dokter2 = Dokter::create([
            'user_id' => $userDokter2->id,
            'poli_id' => $poliGigi->id,
            'nama_dokter' => 'drg. Siti Nurhaliza',
            'spesialis' => 'Gigi',
        ]);
        $dokter3 = Dokter::create([
            'user_id' => $userDokter3->id,
            'poli_id' => $poliAnak->id,
            'nama_dokter' => 'dr. Lina Marlina, Sp.A',
            'spesialis' => 'Anak',
        ]);
        $dokter4 = Dokter::create([
            'user_id' => $userDokter4->id,
            'poli_id' => $poliSaraf->id,
            'nama_dokter' => 'dr. Richard Hartono, Sp.S',
            'spesialis' => 'Saraf',
        ]);

        // =============================================
        // 4. JADWAL DOKTER
        // =============================================
        $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ([$dokter1, $dokter2, $dokter3, $dokter4] as $dokter) {
            foreach ($hariKerja as $hari) {
                JadwalDokter::create([
                    'dokter_id' => $dokter->id,
                    'hari' => $hari,
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '14:00',
                    'kuota' => 30,
                    'is_active' => true,
                ]);
            }
        }

        // =============================================
        // 5. PASIEN (beberapa linked to User)
        // =============================================
        $userPasien1 = User::create([
            'username' => 'budi.santoso',
            'nama_lengkap' => 'Budi Santoso',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
        ]);
        Pasien::create([
            'user_id' => $userPasien1->id,
            'cara_bayar' => 'Umum',
            'no_rm' => 'RM-2026-0001',
            'nik' => '6301010101010001',
            'nama_pasien' => 'Budi Santoso',
            'tempat_lahir' => 'Banjarmasin',
            'tanggal_lahir' => '1985-05-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. A. Yani No. 10, Padang Batung',
            'desa_kelurahan' => 'Padang Batung',
            'rt' => '001',
            'rw' => '002',
            'no_hp' => '081234567890',
        ]);

        $userPasien2 = User::create([
            'username' => 'siti.aminah',
            'nama_lengkap' => 'Siti Aminah',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
        ]);
        Pasien::create([
            'user_id' => $userPasien2->id,
            'cara_bayar' => 'BPJS',
            'no_bpjs' => '0001234567890',
            'no_rm' => 'RM-2026-0002',
            'nik' => '6301010101010002',
            'nama_pasien' => 'Siti Aminah',
            'tempat_lahir' => 'Barabai',
            'tanggal_lahir' => '1990-08-21',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Merdeka No. 25, Padang Batung',
            'desa_kelurahan' => 'Padang Batung',
            'rt' => '003',
            'rw' => '001',
            'no_hp' => '082345678901',
        ]);

        Pasien::create([
            'cara_bayar' => 'Umum',
            'no_rm' => 'RM-2026-0003',
            'nik' => '6301010101010003',
            'nama_pasien' => 'Ahmad Rizki',
            'tempat_lahir' => 'Kandangan',
            'tanggal_lahir' => '2010-03-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Desa Sungai Kupang, Padang Batung',
            'desa_kelurahan' => 'Sungai Kupang',
            'rt' => '002',
            'rw' => '003',
            'no_hp' => '083456789012',
        ]);

        Pasien::create([
            'cara_bayar' => 'BPJS',
            'no_bpjs' => '0001234567893',
            'no_rm' => 'RM-2026-0004',
            'nik' => '6301010101010004',
            'nama_pasien' => 'Hj. Fatimah',
            'tempat_lahir' => 'Amuntai',
            'tanggal_lahir' => '1955-12-01',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Pahlawan No. 5, Padang Batung',
            'desa_kelurahan' => 'Padang Batung',
            'rt' => '005',
            'rw' => '002',
            'no_hp' => '085678901234',
        ]);

        Pasien::create([
            'cara_bayar' => 'Umum',
            'no_rm' => 'RM-2026-0005',
            'nik' => '6301010101010005',
            'nama_pasien' => 'Muhammad Hasan',
            'tempat_lahir' => 'Padang Batung',
            'tanggal_lahir' => '1998-07-20',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Sudirman No. 15, Padang Batung',
            'desa_kelurahan' => 'Padang Batung',
            'rt' => '004',
            'rw' => '001',
            'no_hp' => '086789012345',
        ]);

        // =============================================
        // 6. OBAT (Obat umum Puskesmas)
        // =============================================
        $obats = [
            ['nama_obat' => 'Paracetamol 500mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 500, 'harga_jual' => 500, 'expired_at' => '2027-12-31'],
            ['nama_obat' => 'Amoxicillin 500mg', 'sediaan' => 'Kapsul', 'kemasan' => 'Strip 10', 'satuan' => 'Kapsul', 'stok' => 300, 'harga_jual' => 1000, 'expired_at' => '2027-06-30'],
            ['nama_obat' => 'Ibuprofen 400mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 400, 'harga_jual' => 800, 'expired_at' => '2027-09-30'],
            ['nama_obat' => 'Omeprazole 20mg', 'sediaan' => 'Kapsul', 'kemasan' => 'Strip 10', 'satuan' => 'Kapsul', 'stok' => 200, 'harga_jual' => 1500, 'expired_at' => '2027-08-31'],
            ['nama_obat' => 'Metformin 500mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 250, 'harga_jual' => 1200, 'expired_at' => '2027-10-31'],
            ['nama_obat' => 'Amlodipine 5mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 300, 'harga_jual' => 1000, 'expired_at' => '2027-11-30'],
            ['nama_obat' => 'Cetirizine 10mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 350, 'harga_jual' => 700, 'expired_at' => '2027-07-31'],
            ['nama_obat' => 'Antasida DOEN', 'sediaan' => 'Suspensi', 'kemasan' => 'Botol 100ml', 'satuan' => 'Botol', 'stok' => 100, 'harga_jual' => 5000, 'expired_at' => '2027-05-31'],
            ['nama_obat' => 'OBH (Obat Batuk Hitam)', 'sediaan' => 'Sirup', 'kemasan' => 'Botol 100ml', 'satuan' => 'Botol', 'stok' => 150, 'harga_jual' => 4000, 'expired_at' => '2027-04-30'],
            ['nama_obat' => 'Salep Gentamicin', 'sediaan' => 'Salep', 'kemasan' => 'Tube 10g', 'satuan' => 'Tube', 'stok' => 80, 'harga_jual' => 3000, 'expired_at' => '2027-03-31'],
            ['nama_obat' => 'Dexamethasone 0.5mg', 'sediaan' => 'Tablet', 'kemasan' => 'Strip 10', 'satuan' => 'Tablet', 'stok' => 200, 'harga_jual' => 600, 'expired_at' => '2027-12-31'],
            ['nama_obat' => 'Vitamin B Complex', 'sediaan' => 'Tablet', 'kemasan' => 'Pot 100', 'satuan' => 'Tablet', 'stok' => 1000, 'harga_jual' => 300, 'expired_at' => '2028-01-31'],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }

        // =============================================
        // X. TINDAKAN MEDIS & PENUNJANG (NEW)
        // =============================================
        $tindakans = [
            ['nama_tindakan' => 'Jahit Luka Kecil (1-3 Jahitan)', 'kategori' => 'Tindakan', 'harga' => 50000, 'is_active' => true],
            ['nama_tindakan' => 'Jahit Luka Besar (>3 Jahitan)', 'kategori' => 'Tindakan', 'harga' => 100000, 'is_active' => true],
            ['nama_tindakan' => 'Cabut Gigi Anak', 'kategori' => 'Tindakan', 'harga' => 50000, 'is_active' => true],
            ['nama_tindakan' => 'Cabut Gigi Dewasa', 'kategori' => 'Tindakan', 'harga' => 100000, 'is_active' => true],
            ['nama_tindakan' => 'Pembersihan Karang Gigi (Scaling)', 'kategori' => 'Tindakan', 'harga' => 150000, 'is_active' => true],
            ['nama_tindakan' => 'Cek Gula Darah', 'kategori' => 'Penunjang', 'harga' => 20000, 'is_active' => true],
            ['nama_tindakan' => 'Cek Asam Urat', 'kategori' => 'Penunjang', 'harga' => 25000, 'is_active' => true],
            ['nama_tindakan' => 'Cek Kolesterol', 'kategori' => 'Penunjang', 'harga' => 30000, 'is_active' => true],
            ['nama_tindakan' => 'Nebulizer (Uap)', 'kategori' => 'Tindakan', 'harga' => 50000, 'is_active' => true],
            ['nama_tindakan' => 'Kassa Steril + Verban', 'kategori' => 'BHP', 'harga' => 15000, 'is_active' => true],
        ];

        foreach ($tindakans as $t) {
            \App\Models\Tindakan::create($t);
        }

        // =============================================
        // 7. PENYAKIT (ICD-10 umum Puskesmas)
        // =============================================
        $penyakits = [
            ['kode' => 'J06.9', 'nama_penyakit' => 'Infeksi saluran pernapasan atas akut, tidak spesifik'],
            ['kode' => 'A09', 'nama_penyakit' => 'Diare dan gastroenteritis asal infeksi'],
            ['kode' => 'K29.7', 'nama_penyakit' => 'Gastritis, tidak spesifik'],
            ['kode' => 'I10', 'nama_penyakit' => 'Hipertensi esensial (primer)'],
            ['kode' => 'E11.9', 'nama_penyakit' => 'Diabetes mellitus tipe 2 tanpa komplikasi'],
            ['kode' => 'M54.5', 'nama_penyakit' => 'Nyeri punggung bawah (low back pain)'],
            ['kode' => 'J02.9', 'nama_penyakit' => 'Faringitis akut, tidak spesifik'],
            ['kode' => 'L30.9', 'nama_penyakit' => 'Dermatitis, tidak spesifik'],
            ['kode' => 'K04.0', 'nama_penyakit' => 'Pulpitis'],
            ['kode' => 'K02.1', 'nama_penyakit' => 'Karies dentin'],
            ['kode' => 'R50.9', 'nama_penyakit' => 'Demam, tidak spesifik'],
            ['kode' => 'N39.0', 'nama_penyakit' => 'Infeksi saluran kemih, lokasi tidak spesifik'],
            ['kode' => 'H10.9', 'nama_penyakit' => 'Konjungtivitis, tidak spesifik'],
            ['kode' => 'J00', 'nama_penyakit' => 'Nasofaringitis akut (common cold)'],
            ['kode' => 'B82.9', 'nama_penyakit' => 'Helminthiasis intestinal, tidak spesifik'],
            ['kode' => 'G43.9', 'nama_penyakit' => 'Migrain, tidak spesifik'],
            ['kode' => 'J18.9', 'nama_penyakit' => 'Pneumonia, tidak spesifik'],
            ['kode' => 'E28.9', 'nama_penyakit' => 'Disfungsi ovarium, tidak spesifik'],
            ['kode' => 'R51', 'nama_penyakit' => 'Sakit kepala'],
            ['kode' => 'J45.9', 'nama_penyakit' => 'Asma, tidak spesifik'],
        ];

        foreach ($penyakits as $p) {
            Penyakit::create($p);
        }

        echo "✅ Seeder selesai!\n";
        echo "   - User: " . User::count() . " akun\n";
        echo "   - Poli: " . Poli::count() . "\n";
        echo "   - Dokter: " . Dokter::count() . "\n";
        echo "   - Jadwal: " . JadwalDokter::count() . "\n";
        echo "   - Pasien: " . Pasien::count() . "\n";
        echo "   - Obat: " . Obat::count() . "\n";
        echo "   - Tindakan: " . \App\Models\Tindakan::count() . "\n";
        echo "   - Penyakit: " . Penyakit::count() . "\n";
    }
}
