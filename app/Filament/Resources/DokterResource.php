<?php

namespace App\Filament\Resources;

use App\Models\Dokter;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Dokter';

    protected static ?int $navigationSort = 11;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('kepala');
    }

    public static function canCreate(): bool
    {
        return !auth()->user()?->hasRole('kepala');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return !auth()->user()?->hasRole('kepala');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return !auth()->user()?->hasRole('kepala');
    }

    protected static ?string $pluralModelLabel = 'Dokter';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Dokter')->schema([
                TextInput::make('nama_dokter')->required(),
                TextInput::make('spesialis')->required(),
                Select::make('poli_id')
                    ->relationship('poli', 'nama_poli')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Poli'),
            ])->columns(2),
            Section::make('Akun Sistem')->schema([
                Placeholder::make('info_akun')
                    ->label('Informasi Akun')
                    ->content('Akun login dokter akan dibuat otomatis menggunakan nama dokter (tanpa spasi, huruf kecil) sebagai username dan password default "Dokter123".'),
            ])->visible(fn($operation) => $operation === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama_dokter')->searchable()->sortable(),
            TextColumn::make('poli.nama_poli')->label('Poli')->sortable(),
            TextColumn::make('spesialis')->badge(),
            TextColumn::make('user.username')
                ->label('Username Akun')
                ->badge()
                ->color('success')
                ->placeholder('Belum Dibuat'),
            TextColumn::make('rekam_medis_count')->counts('rekamMedis')->label('Pasien Diperiksa'),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            DokterResource\RelationManagers\JadwalDoktersRelationManager::class,
        ];
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
