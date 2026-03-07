<?php

namespace App\Filament\Resources;

use App\Models\Obat;
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

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Obat';
    protected static ?string $pluralModelLabel = 'Obat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_obat')->required(),
            TextInput::make('satuan')->required(),
            TextInput::make('stok')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama_obat')->searchable()->sortable(),
            TextColumn::make('satuan'),
            TextColumn::make('stok')->badge()->color(fn($state) => $state < 10 ? 'danger' : ($state < 20 ? 'warning' : 'success')),
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
            'index' => ObatResource\Pages\ListObats::route('/'),
            'create' => ObatResource\Pages\CreateObat::route('/create'),
            'edit' => ObatResource\Pages\EditObat::route('/{record}/edit'),
        ];
    }
}
