<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Filament\Resources\PembayaranResource\RelationManagers;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Kasir / Pembayaran';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
    protected static ?string $pluralModelLabel = 'Kasir / Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')->schema([
                    Forms\Components\Select::make('pendaftaran_id')
                        ->relationship('pendaftaran', 'id')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->pasien->nama_pasien} (#{$record->no_antrian}) [{$record->jenis_pembayaran}]")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if (!$state) return;
                            
                            $pendaftaran = \App\Models\Pendaftaran::with(['poli', 'rekamMedis.resep.detailReseps.obat'])->find($state);
                            if (!$pendaftaran) return;

                            // 1. Biaya Pendaftaran & Konsultasi dari Poli
                            $biayaReg = $pendaftaran->poli->biaya_registrasi ?? 0;
                            $biayaKon = $pendaftaran->poli->biaya_konsultasi ?? 0;

                            $set('biaya_pendaftaran', $biayaReg);
                            $set('biaya_konsultasi', $biayaKon);
                            // 2. Hitung Biaya Obat
                            $biayaObat = 0;
                            if ($pendaftaran->rekamMedis && $pendaftaran->rekamMedis->resep) {
                                foreach ($pendaftaran->rekamMedis->resep->detailReseps as $detail) {
                                    $biayaObat += ($detail->obat->harga_jual ?? 0) * $detail->jumlah;
                                }
                            }
                            $set('biaya_obat', $biayaObat);
                            $set('biaya_obat', $biayaObat);

                            // 3. Hitung Biaya Tindakan, Penunjang, BHP, Tambahan
                            $biayaTindakan = 0;
                            $biayaPenunjang = 0;
                            $biayaBhp = 0;
                            $biayaTambahan = 0;

                            if ($pendaftaran->rekamMedis && $pendaftaran->rekamMedis->tindakans) {
                                foreach ($pendaftaran->rekamMedis->tindakans as $tindakan) {
                                    $subtotal = ($tindakan->pivot->harga_snapshot ?? $tindakan->harga) * $tindakan->pivot->jumlah;
                                    
                                    switch ($tindakan->kategori) {
                                        case 'Tindakan':
                                            $biayaTindakan += $subtotal;
                                            break;
                                        case 'Penunjang':
                                            $biayaPenunjang += $subtotal;
                                            break;
                                        case 'BHP':
                                            $biayaBhp += $subtotal;
                                            break;
                                        default:
                                            $biayaTambahan += $subtotal;
                                            break;
                                    }
                                }
                            }

                            $set('biaya_tindakan', $biayaTindakan);
                            $set('biaya_penunjang', $biayaPenunjang);
                            $set('biaya_bhp', $biayaBhp);
                            $set('biaya_tambahan', $biayaTambahan);

                            // 4. Update Total
                            $total = $biayaReg + $biayaKon + $biayaObat + $biayaTindakan + $biayaPenunjang + $biayaBhp + $biayaTambahan;
                            $set('total_bayar', $total);
                        })
                        ->label('Pasien / Antrian'),
                    
                    Forms\Components\TextInput::make('nomor_kartu_bpjs')
                        ->label('Nomor Kartu BPJS')
                        ->placeholder('Contoh: 0001234567890')
                        ->visible(fn (Forms\Get $get): bool => 
                            \App\Models\Pendaftaran::find($get('pendaftaran_id'))?->jenis_pembayaran === 'BPJS'
                        )
                        ->maxLength(20),

                    Forms\Components\TextInput::make('biaya_pendaftaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                    
                    Forms\Components\TextInput::make('biaya_konsultasi')
                        ->numeric()
                        ->prefix('Rp')
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                    
                    Forms\Components\TextInput::make('biaya_obat')
                        ->numeric()
                        ->prefix('Rp')
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                    
                    Forms\Components\TextInput::make('biaya_tindakan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),

                    Forms\Components\TextInput::make('biaya_penunjang')
                        ->label('Biaya Penunjang (Lab/EKG)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),

                    Forms\Components\TextInput::make('biaya_bhp')
                        ->label('Biaya BHP (Kassa/Spuit/dll)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                    
                    Forms\Components\TextInput::make('biaya_tambahan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn (Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),

                    Forms\Components\TextInput::make('total_bayar')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->readonly()
                        ->default(0.00),

                    Forms\Components\Select::make('status_pembayaran')
                        ->options([
                            'Belum Lunas' => 'Belum Lunas',
                            'Lunas' => 'Lunas',
                            'Piutang' => 'Piutang (Klaim)',
                            'Gratis' => 'Gratis (Kebijakan Pemda)',
                        ])
                        ->default('Belum Lunas')
                        ->required(),
                    Forms\Components\Select::make('metode_pembayaran')
                        ->options([
                            'Tunai' => 'Tunai',
                            'BPJS' => 'BPJS',
                            'QRIS' => 'QRIS / Non-Tunai',
                            'Transfer' => 'Transfer Bank',
                        ])
                        ->default('Tunai'),
                ])->columns(2)
            ]);
    }

    public static function updateTotal(Forms\Get $get, Forms\Set $set)
    {
        $total = (float)$get('biaya_pendaftaran') + 
                 (float)$get('biaya_konsultasi') + 
                 (float)$get('biaya_obat') + 
                 (float)$get('biaya_tindakan') + 
                 (float)$get('biaya_penunjang') + 
                 (float)$get('biaya_bhp') + 
                 (float)$get('biaya_tambahan');
        
        $set('total_bayar', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendaftaran.pasien.no_rm')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendaftaran.jenis_pembayaran')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Total')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Lunas' => 'danger',
                        'Piutang' => 'warning',
                        'Gratis' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode'),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Periode Tanggal'),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak_kwitansi')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Pembayaran $record): string => route('pembayaran.cetak-kwitansi', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Pembayaran $record) => $record->status_pembayaran === 'Lunas'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
