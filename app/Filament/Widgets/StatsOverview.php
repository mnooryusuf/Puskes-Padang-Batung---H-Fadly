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
    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return !($user?->hasRole('pasien') ?? false);
    }

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth();

        return [
            Stat::make('Pasien Baru', Pasien::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('Total pasien terdaftar dlm periode')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success'),
            Stat::make('Antrian Aktif', Pendaftaran::whereNotIn('status', ['Selesai', 'Selesai (Meninggal)'])->count())
                ->description('Pasien dlm proses pelayanan')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->color('warning'),
            Stat::make('Obat Stok Menipis', \App\Models\Obat::where('stok', '<', 10)->count())
                ->description('Jumlah obat dengan stok < 10 unit')
                ->descriptionIcon('heroicon-m-beaker', IconPosition::Before)
                ->color('danger'),
            Stat::make('Total Pendapatan', 'Rp ' . Number::format(
                Pembayaran::where('status_pembayaran', 'Lunas')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_bayar'), 
                locale: 'id'
            ))
                ->description('Total pembayaran lunas dlm periode')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('primary'),
        ];
    }
}
