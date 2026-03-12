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

class LaporanDokter extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Poli Saya';
    protected static ?string $title = 'Pusat Laporan Poli Dokter';
    protected static string $view = 'filament.pages.laporan-dokter';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('dokter');
    }

    public function cetak_kunjunganAction(): Action
    {
        return Action::make('cetak_kunjungan')
            ->label('Laporan Kunjungan (Detail)')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.kunjungan', array_merge($data, ['poli_id' => auth()->user()->dokter?->poli_id])));
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
            ->action(fn (array $data) => redirect()->route('laporan.lb1', array_merge($data, ['poli_id' => auth()->user()->dokter?->poli_id])));
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
            ->action(fn (array $data) => redirect()->route('laporan.rekap_tindakan', array_merge($data, ['poli_id' => auth()->user()->dokter?->poli_id])));
    }

    public function cetak_statistik_labAction(): Action
    {
        return Action::make('cetak_statistik_lab')
            ->label('Statistik Layanan Lab')
            ->icon('heroicon-o-beaker')
            ->color('success')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array $data) => redirect()->route('laporan.statistik_lab', array_merge($data, ['poli_id' => auth()->user()->dokter?->poli_id])));
    }
}
