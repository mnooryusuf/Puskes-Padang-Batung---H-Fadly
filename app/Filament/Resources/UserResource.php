<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('username')->required()->unique(ignoreRecord: true),
            TextInput::make('password')->password()->required(fn($operation) => $operation === 'create')->dehydrated(fn($state) => filled($state)),
            Select::make('role')->options(['admin' => 'Admin', 'petugas' => 'Petugas', 'dokter' => 'Dokter', 'apoteker' => 'Apoteker', 'kasir' => 'Kasir', 'kepala' => 'Kepala', 'pasien' => 'Pasien'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('username')->searchable()->sortable(),
            TextColumn::make('role')->badge()->color(fn($state) => match($state) {'admin'=>'danger','dokter'=>'success','petugas'=>'info','apoteker'=>'warning','kasir'=>'success','kepala'=>'primary',default=>'gray'}),
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
            'index' => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
