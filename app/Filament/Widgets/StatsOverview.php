<?php

namespace App\Filament\Widgets;

use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Antrian;
use App\Models\Pembayaran;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();

        return [
            Stat::make('Pasien Baru Hari Ini', Pasien::where('created_at', '>=', $today)->count())
                ->description('Total pasien terdaftar hari ini')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success'),
            Stat::make('Antrian Aktif', Antrian::where('status', 'Menunggu')->count())
                ->description('Pasien menunggu pelayanan')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->color('warning'),
            Stat::make('Obat Stok Menipis', \App\Models\Obat::where('stok', '<', 10)->count())
                ->description('Jumlah obat dengan stok < 10 unit')
                ->descriptionIcon('heroicon-m-beaker', IconPosition::Before)
                ->color('danger'),
            Stat::make('Pendapatan Hari Ini', 'Rp ' . Number::format(Pembayaran::where('status_pembayaran', 'Lunas')->where('created_at', '>=', $today)->sum('total_bayar'), locale: 'id'))
                ->description('Total pembayaran lunas hari ini')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('primary'),
        ];
    }
}
