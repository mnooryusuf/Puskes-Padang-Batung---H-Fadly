<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianKasirResource\Pages;
use App\Models\Antrian;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class AntrianKasirResource extends Resource
{
    protected static ?string $model = Antrian::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Antrian Kasir';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('admin') || $user?->hasRole('kasir');
    }
    protected static ?string $slug = 'antrian-kasir';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('kategori', 'Kasir')->where('status', 'Menunggu')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('kategori', 'Kasir')->where('status', '!=', 'Selesai'))
            ->columns([
                TextColumn::make('nomor_antrian')
                    ->label('No. Antrian')
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'PRIORITAS') ? 'danger' : 'success'),
                TextColumn::make('pendaftaran.pasien.no_rm')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                TextColumn::make('pendaftaran.jenis_pembayaran')
                    ->label('Kategori Bayar')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Waktu Antri')
                    ->time(),
            ])
            ->actions([
                Action::make('panggil')
                    ->label('Panggil')
                    ->icon('heroicon-o-megaphone')
                    ->color('warning')
                    ->extraAttributes(fn (Antrian $record): array => [
                        'onclick' => new \Illuminate\Support\HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var msg = new SpeechSynthesisUtterance('Nomor antrian " . addslashes($record->nomor_antrian) . ", silakan menuju ke Kasir'); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                    ]),
                Action::make('proses_bayar')
                    ->label('Proses Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->url(fn (Antrian $record): string => PembayaranResource::getUrl('create', [
                        'pendaftaran_id' => $record->pendaftaran_id,
                    ]))
                    ->visible(fn (Antrian $record): bool => !$record->pendaftaran->pembayaran),
                Action::make('selesaikan')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Selesai')
                    ->modalDescription('Tandai antrian ini sebagai selesai? Pastikan pembayaran sudah dilakukan.')
                    ->action(function (Antrian $record) {
                        $record->update(['status' => 'Selesai']);
                        $record->pendaftaran->update(['status' => 'Selesai']);
                    })
                    ->visible(fn (Antrian $record): bool => $record->pendaftaran->pembayaran !== null),
            ])
            ->poll('10s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAntrianKasirs::route('/'),
        ];
    }
}
