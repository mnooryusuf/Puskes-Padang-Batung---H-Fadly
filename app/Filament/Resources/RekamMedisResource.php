<?php

namespace App\Filament\Resources;

use App\Models\RekamMedis;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\RekamMedisResource\Pages;

class RekamMedisResource extends Resource
{
    protected static ?string $model = RekamMedis::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Rekam Medis';
    protected static ?string $pluralModelLabel = 'Rekam Medis';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Pemeriksaan')->schema([
                Select::make('pendaftaran_id')
                    ->relationship('pendaftaran', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => "Antrian #{$record->no_antrian} - {$record->pasien->nama_pasien}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('dokter_id')
                    ->relationship('dokter', 'nama_dokter')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('keluhan')->required(),
                Textarea::make('diagnosa')->required(),
                Textarea::make('tindakan')->required(),
            ])->columns(2),

            Section::make('Resep / Obat')
                ->relationship('resep')
                ->schema([
                    Repeater::make('detailReseps')
                        ->relationship()
                        ->schema([
                            Select::make('obat_id')
                                ->relationship('obat', 'nama_obat')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('dosis')
                                ->placeholder('Contoh: 3x1 hari')
                                ->required(),
                            TextInput::make('jumlah')
                                ->numeric()
                                ->required(),
                        ])->columns(3)
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            TextColumn::make('pendaftaran.pasien.nama_pasien')->label('Pasien')->searchable(),
            TextColumn::make('dokter.nama_dokter')->label('Dokter'),
            TextColumn::make('diagnosa')->limit(50),
        ])->actions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekamMedis::route('/'),
            'create' => Pages\CreateRekamMedis::route('/create'),
            'edit' => Pages\EditRekamMedis::route('/{record}/edit'),
        ];
    }
}
