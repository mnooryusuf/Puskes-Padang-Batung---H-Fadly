<?php

namespace App\Filament\Widgets;

use App\Models\Pendaftaran;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class KunjunganPasienChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Kunjungan Pasien (7 Hari Terakhir)';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }

    protected function getData(): array
    {
        $startDate = \Carbon\Carbon::parse($this->filters['startDate'] ?? now()->subDays(6));
        $endDate = \Carbon\Carbon::parse($this->filters['endDate'] ?? now());
        
        $data = [];
        $labels = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->format('d M');
            $data[] = Pendaftaran::whereDate('created_at', '=', $dateString, 'and')->count();
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pasien',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#00796B',
                    'backgroundColor' => 'rgba(0, 121, 107, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
