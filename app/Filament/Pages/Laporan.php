<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Pusat Laporan';
    protected static string $view = 'filament.pages.laporan';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('kepala_puskesmas') || auth()->user()->hasRole('petugas');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_lplpo')
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
                ->action(fn (array $data) => redirect()->route('laporan.lplpo', $data)),

            Action::make('cetak_lra')
                ->label('LRA (Keuangan)')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->form([
                    DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                    DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
                ])
                ->action(fn (array $data) => redirect()->route('laporan.lra', $data)),

            Action::make('cetak_kunjungan')
                ->label('Statistik Kunjungan')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->form([
                    DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                    DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
                ])
                ->action(fn (array $data) => redirect()->route('laporan.kunjungan', $data)),

            Action::make('cetak_lb1')
                ->label('LB1 (10 Besar Penyakit)')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning')
                ->form([
                    DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                    DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
                ])
                ->action(fn (array $data) => redirect()->route('laporan.lb1', $data)),
        ];
    }
}
