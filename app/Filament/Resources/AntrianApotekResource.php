<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianApotekResource\Pages;
use App\Models\Antrian;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class AntrianApotekResource extends Resource
{
    protected static ?string $model = Antrian::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Antrian Apotek';
    protected static ?string $slug = 'antrian-apotek';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('kategori', 'Obat')->where('status', 'Menunggu')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('kategori', 'Obat')->where('status', '!=', 'Selesai'))
            ->columns([
                TextColumn::make('nomor_antrian')
                    ->label('No. Antrian')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                TextColumn::make('pendaftaran.rekamMedis.resep.id')
                    ->label('Resep')
                    ->formatStateUsing(fn ($state) => $state ? "Resep #$state" : 'Tidak Ada'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'Menunggu' ? 'gray' : 'info'),
                TextColumn::make('created_at')
                    ->label('Waktu Antri')
                    ->time(),
            ])
            ->actions([
                Action::make('serahkan_obat')
                    ->label('Serahkan Obat')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyerahan Obat')
                    ->modalDescription('Pastikan obat sudah diserahkan kepada pasien. Pasien akan otomatis diarahkan ke Kasir.')
                    ->action(function (Antrian $record) {
                        $record->update(['status' => 'Selesai']);
                        
                        // Update global pendaftaran status
                        $record->pendaftaran->update(['status' => 'Menunggu Pembayaran']);
                        
                        // Ensure there is a cashier queue
                        Antrian::firstOrCreate(
                            [
                                'pendaftaran_id' => $record->pendaftaran_id,
                                'kategori' => 'Kasir',
                            ],
                            [
                                'nomor_antrian' => Antrian::generateNomor('Kasir'),
                                'status' => 'Menunggu',
                            ]
                        );
                    }),
            ])
            ->poll('10s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAntrianApoteks::route('/'),
        ];
    }
}
