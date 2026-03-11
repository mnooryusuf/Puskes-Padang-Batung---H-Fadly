<?php

namespace App\Filament\Widgets;

use App\Models\Pendaftaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AntrianPoliWidget extends BaseWidget
{
    protected static ?string $heading = 'Antrian Pasien di Poli Anda';
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('admin') || $user?->hasRole('dokter');
    }

    public function table(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $poliId = $user->dokter?->poli_id;

        return $table
            ->query(
                Pendaftaran::query()
                    ->when($poliId, fn ($q) => $q->where('poli_id', $poliId))
                    ->whereDate('tanggal_daftar', now()->toDateString())
                    ->whereIn('status', ['Menunggu Poli', 'Pemeriksaan'])
                    ->orderBy('no_antrian', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('no_antrian')
                    ->label('No')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('pasien.no_rm')
                    ->label('No. RM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('jenis_pembayaran')
                    ->badge()
                    ->color(fn ($state) => $state === 'BPJS' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Poli' => 'warning',
                        'Pemeriksaan' => 'info',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('panggil')
                    ->label('Tangani')
                    ->icon('heroicon-m-hand-raised')
                    ->color('success')
                    ->url(fn (Pendaftaran $record) => \App\Filament\Resources\RekamMedisResource::getUrl('create', ['pendaftaran_id' => $record->id]))
                    ->visible(fn ($record) => $record->status === 'Menunggu Poli'),
            ])
            ->paginated(false);
    }
}
