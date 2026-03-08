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

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Kasir / Pembayaran';
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

                            // 3. Update Total
                            $total = $biayaReg + $biayaKon + $biayaObat;
                            $set('total_bayar', $total);
                        })
                        ->label('Pasien / Antrian'),
                    
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
                        ])
                        ->required(),
                    Forms\Components\Select::make('metode_pembayaran')
                        ->options([
                            'Tunai' => 'Tunai',
                            'BPJS' => 'BPJS',
                            'Transfer' => 'Transfer',
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
