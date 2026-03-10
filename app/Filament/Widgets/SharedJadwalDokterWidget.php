<?php

namespace App\Filament\Widgets;

use App\Models\JadwalDokter;
use App\Models\Pendaftaran;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class SharedJadwalDokterWidget extends Widget
{
    protected static string $view = 'filament.widgets.shared-jadwal-dokter-widget';
    protected int | string | array $columnSpan = 'full';

    public function getJadwalHariIni()
    {
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
        $tanggalSekarang = Carbon::now()->format('Y-m-d');

        $jadwals = JadwalDokter::with(['dokter.poli'])
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->get();

        // Kumpulkan berdasarkan Poli
        $polis = [];
        foreach ($jadwals as $jadwal) {
            $poliId = $jadwal->dokter->poli_id;
            if (!isset($polis[$poliId])) {
                $polis[$poliId] = [
                    'nama_poli' => $jadwal->dokter->poli->nama_poli ?? 'Tanpa Poli',
                    'total_kuota' => 0,
                    'dokters' => [],
                    'terdaftar' => Pendaftaran::whereDate('tanggal_daftar', $tanggalSekarang)
                                            ->where('poli_id', $poliId)
                                            ->count(),
                ];
            }
            
            $polis[$poliId]['total_kuota'] += $jadwal->kuota;
            $polis[$poliId]['dokters'][] = [
                'nama' => $jadwal->dokter->nama_dokter,
                'jam' => $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai,
            ];
        }

        return [
            'hari' => $hariIni,
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            'polis' => collect($polis)->values()
        ];
    }
}
