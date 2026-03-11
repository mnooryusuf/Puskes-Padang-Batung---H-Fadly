<?php

namespace App\Filament\Widgets;

use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\JadwalDokter;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class MonitorKuotaWidget extends BaseWidget
{
    protected static ?string $heading = 'Monitoring Kuota Poli Hari Ini';
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('admin') || $user?->hasRole('petugas');
    }

    public function table(Table $table): Table
    {
        $today = now()->toDateString();
        $hari = Carbon::parse($today)->locale('id')->isoFormat('dddd');

        return $table
            ->query(
                Poli::query()
                    ->whereHas('dokters.jadwalDokters', function ($query) use ($hari) {
                        $query->where('hari', $hari)->where('is_active', true);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_poli')
                    ->label('Nama Poli')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('dokters_count')
                    ->label('Dokter Aktif')
                    ->state(function (Poli $record) use ($hari) {
                        return JadwalDokter::whereHas('dokter', fn($q) => $q->where('poli_id', $record->id))
                            ->where('hari', $hari)
                            ->where('is_active', true)
                            ->count();
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_kuota')
                    ->label('Total Kuota')
                    ->state(function (Poli $record) use ($hari) {
                        return JadwalDokter::whereHas('dokter', fn($q) => $q->where('poli_id', $record->id))
                            ->where('hari', $hari)
                            ->where('is_active', true)
                            ->sum('kuota');
                    })
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('terdaftar')
                    ->label('Terdaftar')
                    ->state(function (Poli $record) use ($today) {
                        return Pendaftaran::where('poli_id', $record->id)
                            ->whereDate('tanggal_daftar', $today)
                            ->count();
                    })
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('sisa')
                    ->label('Sisa Kuota')
                    ->state(function (Poli $record) use ($today, $hari) {
                        $total = JadwalDokter::whereHas('dokter', fn($q) => $q->where('poli_id', $record->id))
                            ->where('hari', $hari)
                            ->where('is_active', true)
                            ->sum('kuota');
                        $terdaftar = Pendaftaran::where('poli_id', $record->id)
                            ->whereDate('tanggal_daftar', $today)
                            ->count();
                        return max(0, $total - $terdaftar);
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('persentase')
                    ->label('Okupansi')
                    ->state(function (Poli $record) use ($today, $hari) {
                        $total = JadwalDokter::whereHas('dokter', fn($q) => $q->where('poli_id', $record->id))
                            ->where('hari', $hari)
                            ->where('is_active', true)
                            ->sum('kuota');
                        if ($total <= 0) return '0%';
                        $terdaftar = Pendaftaran::where('poli_id', $record->id)
                            ->whereDate('tanggal_daftar', $today)
                            ->count();
                        $percent = ($terdaftar / $total) * 100;
                        return round($percent) . '%';
                    })
                    ->badge()
                    ->color(fn ($state) => (int) $state >= 100 ? 'danger' : ((int) $state >= 80 ? 'warning' : 'success'))
                    ->alignment('center'),
            ])
            ->paginated(false);
    }
}
