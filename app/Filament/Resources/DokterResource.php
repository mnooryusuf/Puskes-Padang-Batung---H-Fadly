<?php

namespace App\Filament\Resources;

use App\Models\Dokter;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Dokter';
    protected static ?string $pluralModelLabel = 'Dokter';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_dokter')->required(),
            TextInput::make('spesialis')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama_dokter')->searchable()->sortable(),
            TextColumn::make('spesialis')->badge(),
            TextColumn::make('rekam_medis_count')->counts('rekamMedis')->label('Pasien Diperiksa'),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DokterResource\Pages\ListDokters::route('/'),
            'create' => DokterResource\Pages\CreateDokter::route('/create'),
            'edit' => DokterResource\Pages\EditDokter::route('/{record}/edit'),
        ];
    }
}
