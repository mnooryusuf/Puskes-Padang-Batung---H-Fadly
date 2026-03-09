<?php

namespace App\Filament\Widgets;

use App\Models\Pendaftaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RiwayatBerobatWidget extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Berobat Saya';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('pasien') ?? false;
    }

    public function table(Table $table): Table
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $pasien = $user?->pasien;

        return $table
            ->query(
                Pendaftaran::query()
                    ->where('pasien_id', $pasien?->id ?? 0)
                    ->orderBy('tanggal_daftar', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_daftar')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_antrian')
                    ->label('No. Antrian')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('poli.nama_poli')
                    ->label('Poli Tujuan'),
                Tables\Columns\TextColumn::make('jenis_kunjungan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'info',
                        'Lama' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('rekamMedis.penyakit.nama_penyakit')
                    ->label('Diagnosis')
                    ->limit(40)
                    ->placeholder('Belum diperiksa'),
                Tables\Columns\TextColumn::make('rekamMedis.tindakan')
                    ->label('Tindakan')
                    ->limit(30)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Poli' => 'warning',
                        'Pemeriksaan' => 'info',
                        'Menunggu Obat' => 'warning',
                        'Menunggu Pembayaran' => 'success',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Detail')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Detail Riwayat Kunjungan')
                    ->modalContent(fn ($record) => view('filament.widgets.riwayat-berobat-row', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->paginated([5, 10, 25]);

    }
}
