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
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth();

        // Role Dokter Stats
        if ($user->hasRole('dokter')) {
            $dokter = $user->dokter;
            $poliId = $dokter?->poli_id;
            $today = now()->toDateString();
            
            return [
                Stat::make('Pasien Menunggu (Poli)', Pendaftaran::where('poli_id', $poliId)
                    ->where('status', 'Menunggu Poli')
                    ->whereDate('tanggal_daftar', $today)
                    ->count())
                    ->description('Pasien menunggu di poli Anda')
                    ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                    ->color('warning'),
                Stat::make('Selesai (Hari Ini)', Pendaftaran::where('poli_id', $poliId)
                    ->where('status', 'Selesai')
                    ->whereDate('tanggal_daftar', $today)
                    ->count())
                    ->description('Pasien Anda tangani hari ini')
                    ->descriptionIcon('heroicon-m-check-circle', IconPosition::Before)
                    ->color('success'),
                Stat::make('Pasien Anda (Bulan Ini)', Pendaftaran::where('poli_id', $poliId)
                    ->whereBetween('tanggal_daftar', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count())
                    ->description('Total pasien dilayani bulan ini')
                    ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                    ->color('info'),
            ];
        }

        // Role Petugas (Pendaftaran) Stats
        if ($user->hasRole('petugas')) {
            $today = now()->toDateString();
            
            return [
                Stat::make('Pendaftaran Hari Ini', Pendaftaran::whereDate('tanggal_daftar', $today)->count())
                    ->description('Total pasien didaftarkan hari ini')
                    ->descriptionIcon('heroicon-m-clipboard-document-check', IconPosition::Before)
                    ->color('primary'),
                Stat::make('Menunggu Pelayanan', Pendaftaran::where('status', 'Menunggu Poli')->count())
                    ->description('Pasien belum dipanggil poli')
                    ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                    ->color('warning'),
                Stat::make('Pasien Baru (Hari Ini)', Pasien::whereDate('created_at', $today)->count())
                    ->description('Rekam medis baru dibuat hari ini')
                    ->descriptionIcon('heroicon-m-user-plus', IconPosition::Before)
                    ->color('success'),
                Stat::make('Sisa Kuota Total', function() use ($today) {
                    $hari = \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd');
                    $totalKuota = \App\Models\JadwalDokter::where('hari', $hari)->where('is_active', true)->sum('kuota');
                    $terdaftar = Pendaftaran::whereDate('tanggal_daftar', $today)->count();
                    return max(0, $totalKuota - $terdaftar);
                })
                    ->description('Sisa kuota pendaftaran hari ini')
                    ->descriptionIcon('heroicon-m-ticket', IconPosition::Before)
                    ->color('info'),
            ];
        }

        // Admin/Default Stats
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
