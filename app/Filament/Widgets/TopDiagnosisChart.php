<?php

namespace App\Filament\Widgets;

use App\Models\RekamMedis;
use App\Models\Penyakit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopDiagnosisChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Diagnosis di Poli Anda (Bulan Ini)';
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('admin') || $user?->hasRole('dokter');
    }

    protected function getData(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $poliId = $user->dokter?->poli_id;

        $topDiagnoses = RekamMedis::query()
            ->join('pendaftaran', 'rekam_medis.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('penyakit', 'rekam_medis.penyakit_id', '=', 'penyakit.id')
            ->select('penyakit.nama_penyakit', DB::raw('count(*) as total'))
            ->when($poliId, fn ($q) => $q->where('pendaftaran.poli_id', $poliId))
            ->whereBetween('rekam_medis.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('penyakit.nama_penyakit')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus',
                    'data' => $topDiagnoses->pluck('total')->toArray(),
                    'backgroundColor' => ['#00796B', '#FF9800', '#F44336', '#2196F3', '#9C27B0'],
                ],
            ],
            'labels' => $topDiagnoses->pluck('nama_penyakit')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
