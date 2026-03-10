<?php
$content = file_get_contents('app/Filament/Pages/Laporan.php');

// We'll replace the getCachedActions method entirely
$newContent = preg_replace('/protected function getCachedActions\(\): array\s*\{\s*return \[(.*)\];\s*\}/s', '// REPLACED', $content);

// The regex above might be too brittle, let's use string manipulation

$startMarker = 'protected function getCachedActions(): array';
$posStart = strpos($content, $startMarker);
if ($posStart === false) {
    echo "Marker not found\n";
    exit;
}

$beforeFunction = substr($content, 0, $posStart);

$newFunctions = <<<PHP
    public function cetakLplpoAction(): Action
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
                    ->options(collect(range(now()->year - 2, now()->year + 1))->mapWithKeys(fn(\$y) => [\$y => \$y])->all())
                    ->default(now()->year)
                    ->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.lplpo', \$data));
    }

    public function cetakLraAction(): Action
    {
        return Action::make('cetak_lra')
            ->label('LRA (Keuangan)')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.lra', \$data));
    }

    public function cetakKunjunganAction(): Action
    {
        return Action::make('cetak_kunjungan')
            ->label('Statistik Kunjungan')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.kunjungan', \$data));
    }

    public function cetakKunjunganPoliAction(): Action
    {
        return Action::make('cetak_kunjungan_poli')
            ->label('Kunjungan per Poli')
            ->icon('heroicon-o-building-office-2')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.kunjungan_poli', \$data));
    }

    public function cetakPasienBaruAction(): Action
    {
        return Action::make('cetak_pasien_baru')
            ->label('Pasien Baru vs Lama')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.pasien_baru', \$data));
    }

    public function cetakRekapTindakanAction(): Action
    {
        return Action::make('cetak_rekap_tindakan')
            ->label('Rekap Tindakan Medis')
            ->icon('heroicon-o-document-magnifying-glass')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.rekap_tindakan', \$data));
    }

    public function cetakStatistikLabAction(): Action
    {
        return Action::make('cetak_statistik_lab')
            ->label('Statistik Lab')
            ->icon('heroicon-o-beaker')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.statistik_lab', \$data));
    }

    public function cetakPasienStatusAction(): Action
    {
        return Action::make('cetak_pasien_status')
            ->label('Status Pulang/Rujuk')
            ->icon('heroicon-o-truck')
            ->color('info')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.pasien_status', \$data));
    }

    public function cetakObatExpiredAction(): Action
    {
        return Action::make('cetak_obat_expired')
            ->label('Obat Kadaluwarsa')
            ->icon('heroicon-o-calendar-days')
            ->color('danger')
            ->action(fn () => redirect()->route('laporan.obat_expired'));
    }

    public function cetakObatAnalisaAction(): Action
    {
        return Action::make('cetak_obat_analisa')
            ->label('Analisa Fast/Slow Moving')
            ->icon('heroicon-o-chart-pie')
            ->color('warning')
            ->action(fn () => redirect()->route('laporan.obat_analisa'));
    }

    public function cetakPendapatanAction(): Action
    {
        return Action::make('cetak_pendapatan')
            ->label('Rekap Pendapatan Kasir')
            ->icon('heroicon-o-currency-dollar')
            ->color('success')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.pendapatan', \$data));
    }

    public function cetakDistribusiPenyakitAction(): Action
    {
        return Action::make('cetak_distribusi_penyakit')
            ->label('Distribusi Penyakit (Umur/JK)')
            ->icon('heroicon-o-chart-bar-square')
            ->color('warning')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.distribusi_penyakit', \$data));
    }

    public function cetakLb1Action(): Action
    {
        return Action::make('cetak_lb1')
            ->label('LB1 (10 Besar Penyakit)')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('warning')
            ->form([
                DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
            ])
            ->action(fn (array \$data) => redirect()->route('laporan.lb1', \$data));
    }
PHP;

file_put_contents('app/Filament/Pages/Laporan.php', $beforeFunction . $newFunctions . "\n}\n");
echo "Updated Laporan.php\n";

