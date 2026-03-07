<?php

namespace App\Filament\Resources;

use App\Models\Pasien;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Pasien';
    protected static ?string $pluralModelLabel = 'Pasien';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('no_rm')->label('No. RM')->required()->unique(ignoreRecord: true),
            TextInput::make('nama_pasien')->required(),
            DatePicker::make('tanggal_lahir')->required(),
            Select::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])->required(),
            TextInput::make('no_hp')->tel()->required(),
            Textarea::make('alamat')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no_rm')->label('No. RM')->searchable()->sortable(),
            TextColumn::make('nama_pasien')->searchable()->sortable(),
            TextColumn::make('tanggal_lahir')->date(),
            TextColumn::make('jenis_kelamin'),
            TextColumn::make('no_hp'),
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
            'index' => PasienResource\Pages\ListPasiens::route('/'),
            'create' => PasienResource\Pages\CreatePasien::route('/create'),
            'edit' => PasienResource\Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}
