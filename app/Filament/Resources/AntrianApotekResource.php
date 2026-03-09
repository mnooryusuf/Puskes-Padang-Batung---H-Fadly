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

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
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
                TextColumn::make('pendaftaran.pasien.no_rm')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                TextColumn::make('pendaftaran.rekamMedis.resep.id')
                    ->label('Resep')
                    ->formatStateUsing(fn ($state) => $state ? "Resep #$state" : 'Tidak Ada'),
                TextColumn::make('pendaftaran.rekamMedis.resep.status_pengambilan')
                    ->label('Status Resep')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Diproses' => 'warning',
                        'Siap Diambil' => 'info',
                        'Sudah Diserahkan' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu Antri')
                    ->time(),
            ])
            ->actions([
                Action::make('panggil')
                    ->label('Panggil')
                    ->icon('heroicon-o-megaphone')
                    ->color('info')
                    ->extraAttributes(fn (Antrian $record): array => [
                        'onclick' => new \Illuminate\Support\HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var msg = new SpeechSynthesisUtterance('Nomor antrian " . $record->nomor_antrian . ", silakan menuju ke Apotek'); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                    ]),
                Action::make('proses')
                    ->label('Proses')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->action(fn (Antrian $record) => $record->pendaftaran->rekamMedis->resep->update(['status_pengambilan' => 'Diproses']))
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep?->status_pengambilan === 'Menunggu'),

                Action::make('cetak_etiket')
                    ->label('Etiket')
                    ->icon('heroicon-o-tag')
                    ->color('gray')
                    ->url(fn (Antrian $record): ?string => $record->pendaftaran->rekamMedis?->resep ? route('resep.cetak-etiket', $record->pendaftaran->rekamMedis->resep) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep !== null),

                Action::make('siap')
                    ->label('Siap Diambil')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->action(fn (Antrian $record) => $record->pendaftaran->rekamMedis->resep->update(['status_pengambilan' => 'Siap Diambil']))
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep?->status_pengambilan === 'Diproses'),

                Action::make('serahkan_obat')
                    ->label('Serahkan Obat')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyerahan Obat')
                    ->modalDescription('Pastikan obat sudah diserahkan kepada pasien. Stok akan dikurangi otomatis dan pasien diarahkan ke Kasir.')
                    ->action(function (Antrian $record) {
                        $resep = $record->pendaftaran->rekamMedis?->resep;
                        if ($resep) {
                            // Update Status Resep
                            $resep->update(['status_pengambilan' => 'Sudah Diserahkan']);

                            // Kurangi Stok Otomatis
                            foreach ($resep->detailReseps as $detail) {
                                $obat = $detail->obat;
                                if ($obat) {
                                    $obat->decrement('stok', $detail->jumlah);
                                }
                            }
                        }

                        // Update Status Antrian
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
                    })
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep?->status_pengambilan === 'Siap Diambil'),
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
