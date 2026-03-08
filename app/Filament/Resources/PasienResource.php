<?php

namespace App\Filament\Resources;

use App\Models\Pasien;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
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
            Section::make('Data Pasien')->schema([
                TextInput::make('no_rm')
                    ->label('No. RM')
                    ->default(fn () => Pasien::generateNoRm())
                    ->readonly()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->length(16)
                    ->numeric()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_pasien')->required(),
                DatePicker::make('tanggal_lahir')->required(),
                Select::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])->required(),
                TextInput::make('no_hp')->tel()->required(),
                Textarea::make('alamat')->required(),
            ])->columns(2),
            Section::make('Akun Sistem')->schema([
                Placeholder::make('info_akun')
                    ->label('Informasi Akun')
                    ->content('Akun login pasien akan dibuat otomatis menggunakan No. RM sebagai username dan password default "Puskes[No. RM]".'),
            ])->visible(fn($operation) => $operation === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no_rm')->label('No. RM')->searchable()->sortable(),
            TextColumn::make('nik')->label('NIK')->searchable()->sortable(),
            TextColumn::make('nama_pasien')->searchable()->sortable(),
            TextColumn::make('tanggal_lahir')->date(),
            TextColumn::make('jenis_kelamin')->label('JK'),
            TextColumn::make('no_hp')->label('No. HP'),
            TextColumn::make('user.username')
                ->label('Username Akun')
                ->badge()
                ->color('success')
                ->placeholder('Belum Dibuat'),
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
