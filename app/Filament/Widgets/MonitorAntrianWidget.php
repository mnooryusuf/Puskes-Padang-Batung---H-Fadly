<?php

namespace App\Filament\Widgets;

use App\Models\Antrian;
use App\Models\Poli;
use Filament\Widgets\Widget;

class MonitorAntrianWidget extends Widget
{
    protected static string $view = 'filament.widgets.monitor-antrian-widget';
    protected int | string | array $columnSpan = 'full';
    
    // Refresh otomatis setiap 10 detik
    public ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('pasien') ?? false;
    }

    protected function getViewData(): array
    {
        $polis = Poli::all();
        $antrianSaatIni = [];

        foreach ($polis as $poli) {
            // Carier antrian dengan status Dipanggil atau Pemeriksaan hari ini
            $dipanggil = Antrian::where('poli_id', $poli->id)
                ->whereDate('created_at', now()->toDateString())
                ->whereIn('status', ['Dipanggil', 'Pemeriksaan'])
                ->orderBy('updated_at', 'desc')
                ->first();
            
            // Jika tidak ada yang dipanggil, cari antrian terakhir yang selesai
            if (!$dipanggil) {
                $dipanggil = Antrian::where('poli_id', $poli->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->where('status', 'Selesai')
                    ->orderBy('updated_at', 'desc')
                    ->first();
            }

            $antrianSaatIni[$poli->id] = $dipanggil ? $dipanggil->nomor_antrian : '-';
        }

        return [
            'polis' => $polis,
            'antrianSaatIni' => $antrianSaatIni,
        ];
    }
}
