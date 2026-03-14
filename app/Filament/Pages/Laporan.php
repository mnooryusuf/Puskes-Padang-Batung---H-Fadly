<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Laporan extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Pusat Laporan';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Pusat Laporan';
    protected static string $view = 'filament.pages.laporan';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('kepala') || auth()->user()->hasRole('petugas') || auth()->user()->hasRole('apoteker') || auth()->user()->hasRole('kasir');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function cetak_lplpoAction(): Action
    {
        return Action::make('cetak_lplpo')
            ->label('LPLPO (Farmasi)')
            ->icon('heroicon-o-beaker')
            ->color('info')
            ->form([
                Select::make('month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])
                    ->default(now()->month)
                    ->required(),
                Select::make('year')
                    ->label('Tahun')
                    ->options(collect(range(now()->year - 2, now()->year + 1))->mapWithKeys(fn($y) => [$y => $y])->all())
                    ->default(now()->year)
                    ->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.lplpo', $data));
    }

    public function cetak_lraAction(): Action
    {
        return Action::make('cetak_lra')
            ->label('LRA (Keuangan)')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.lra', $data));
    }

    public function cetak_kunjunganAction(): Action
    {
        return Action::make('cetak_kunjungan')
            ->label('Statistik Kunjungan')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.kunjungan', $data));
    }

    public function cetak_kunjungan_poliAction(): Action
    {
        return Action::make('cetak_kunjungan_poli')
            ->label('Kunjungan per Poli')
            ->icon('heroicon-o-building-office-2')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.kunjungan_poli', $data));
    }

    public function cetak_pasien_baruAction(): Action
    {
        return Action::make('cetak_pasien_baru')
            ->label('Pasien Baru vs Lama')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.pasien_baru', $data));
    }

    public function cetak_rekap_tindakanAction(): Action
    {
        return Action::make('cetak_rekap_tindakan')
            ->label('Rekap Tindakan Medis')
            ->icon('heroicon-o-document-magnifying-glass')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.rekap_tindakan', $data));
    }

    public function cetak_statistik_labAction(): Action
    {
        return Action::make('cetak_statistik_lab')
            ->label('Statistik Lab')
            ->icon('heroicon-o-beaker')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.statistik_lab', $data));
    }

    public function cetak_pasien_statusAction(): Action
    {
        return Action::make('cetak_pasien_status')
            ->label('Status Pulang/Rujuk')
            ->icon('heroicon-o-truck')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.pasien_status', $data));
    }

    public function cetak_obat_expiredAction(): Action
    {
        return Action::make('cetak_obat_expired')
            ->label('Obat Kadaluwarsa')
            ->icon('heroicon-o-calendar-days')
            ->color('danger')
            ->action(fn () => redirect()->route('laporan.obat_expired'));
    }

    public function cetak_obat_analisaAction(): Action
    {
        return Action::make('cetak_obat_analisa')
            ->label('Analisa Fast/Slow Moving')
            ->icon('heroicon-o-chart-pie')
            ->color('warning')
            ->action(fn () => redirect()->route('laporan.obat_analisa'));
    }

    public function cetak_pendapatanAction(): Action
    {
        return Action::make('cetak_pendapatan')
            ->label('Rekap Pendapatan Kasir')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.pendapatan', $data));
    }

    public function cetak_distribusi_penyakitAction(): Action
    {
        return Action::make('cetak_distribusi_penyakit')
            ->label('Distribusi Penyakit (Umur/JK)')
            ->icon('heroicon-o-chart-bar-square')
            ->color('warning')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.distribusi_penyakit', $data));
    }

    public function cetak_lb1Action(): Action
    {
        return Action::make('cetak_lb1')
            ->label('LB1 (10 Besar Penyakit)')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('warning')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.lb1', $data));
    }
}
