<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\Dokter;
use App\Models\Pasien;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $modelLabel = 'Pengguna';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }
    protected static ?string $pluralModelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('username')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->required(fn($operation) => $operation === 'create')
                ->dehydrated(fn($state) => filled($state)),

            Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'petugas' => 'Petugas',
                    'dokter' => 'Dokter',
                    'apoteker' => 'Apoteker',
                    'kasir' => 'Kasir',
                    'kepala' => 'Kepala',
                    'pasien' => 'Pasien',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    // Reset nama when role changes
                    $set('nama_lengkap', null);
                    $set('dokter_selector', null);
                    $set('pasien_selector', null);
                }),

            // Dokter selector - hanya tampil jika role = dokter
            Select::make('dokter_selector')
                ->label('Pilih Dokter')
                ->options(Dokter::pluck('nama_dokter', 'id'))
                ->searchable()
                ->visible(fn (callable $get) => $get('role') === 'dokter')
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $dokter = Dokter::find($state);
                        $set('nama_lengkap', $dokter?->nama_dokter);
                    }
                })
                ->dehydrated(false)
                ->helperText('Nama lengkap akan terisi otomatis'),

            // Pasien selector - hanya tampil jika role = pasien
            Select::make('pasien_selector')
                ->label('Pilih Pasien')
                ->options(Pasien::pluck('nama_pasien', 'id'))
                ->searchable()
                ->visible(fn (callable $get) => $get('role') === 'pasien')
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $pasien = Pasien::find($state);
                        $set('nama_lengkap', $pasien?->nama_pasien);
                    }
                })
                ->dehydrated(false)
                ->helperText('Nama lengkap akan terisi otomatis'),

            TextInput::make('nama_lengkap')
                ->label('Nama Lengkap')
                ->required()
                ->placeholder(fn (callable $get) => match ($get('role')) {
                    'dokter' => 'Pilih dokter di atas',
                    'pasien' => 'Pilih pasien di atas',
                    default => 'Masukkan nama lengkap',
                })
                ->helperText(fn (callable $get) => match ($get('role')) {
                    'dokter', 'pasien' => '✨ Nama terisi otomatis dari data yang dipilih',
                    default => null,
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('username')->searchable()->sortable(),
            TextColumn::make('nama_lengkap')->label('Nama Lengkap')->searchable()->sortable(),
            TextColumn::make('role')->badge()->color(fn($state) => match($state) {
                'admin' => 'danger',
                'dokter' => 'success',
                'petugas' => 'info',
                'apoteker' => 'warning',
                'kasir' => 'success',
                'kepala' => 'primary',
                default => 'gray',
            }),
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
