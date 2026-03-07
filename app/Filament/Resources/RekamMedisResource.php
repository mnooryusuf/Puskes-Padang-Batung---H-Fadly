<?php

namespace App\Filament\Resources;

use App\Models\RekamMedis;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

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
            Select::make('pendaftaran_id')->relationship('pendaftaran', 'id')->getOptionLabelFromRecordUsing(fn($record) => "Antrian #{$record->no_antrian} - {$record->pasien->nama_pasien}")->required(),
            Select::make('dokter_id')->relationship('dokter', 'nama_dokter')->required(),
            Textarea::make('keluhan')->required(),
            Textarea::make('diagnosa')->required(),
            Textarea::make('tindakan')->required(),
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
            'index' => RekamMedisResource\Pages\ListRekamMedis::route('/'),
            'create' => RekamMedisResource\Pages\CreateRekamMedis::route('/create'),
            'edit' => RekamMedisResource\Pages\EditRekamMedis::route('/{record}/edit'),
        ];
    }
}
