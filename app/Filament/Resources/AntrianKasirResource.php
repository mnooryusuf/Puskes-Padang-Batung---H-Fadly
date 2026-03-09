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

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
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
                    ->color('success'),
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
                        'onclick' => new \Illuminate\Support\HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var unit = '" . addslashes($record->kategori === 'Poli' ? 'Poli ' . ($record->poli?->nama_poli ?? '') : ($record->kategori === 'Obat' ? 'Apotek' : $record->kategori)) . "'; var msg = new SpeechSynthesisUtterance('Nomor antrian " . addslashes($record->nomor_antrian) . ", silakan menuju ke ' + unit); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                    ]),
                Action::make('proses_bayar')
                    ->label('Proses Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->url(fn (Antrian $record): string => PembayaranResource::getUrl('create', [
                        'pendaftaran_id' => $record->pendaftaran_id,
                    ])),
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
