<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoliResource\Pages;
use App\Filament\Resources\PoliResource\RelationManagers;
use App\Models\Poli;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoliResource extends Resource
{
    protected static ?string $model = Poli::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Poli';

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }

    protected static ?string $pluralModelLabel = 'Poli';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Poli')->schema([
                    Forms\Components\TextInput::make('nama_poli')
                        ->label('Nama Poli')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->prefix('Poli')
                        ->placeholder('Contoh: Umum, Gigi, Saraf, Anak')
                        ->helperText('Ketik nama spesialisasi saja, awalan "Poli" otomatis ditambahkan')
                        ->dehydrateStateUsing(function ($state) {
                            if ($state && !str_starts_with($state, 'Poli ')) {
                                return 'Poli ' . $state;
                            }
                            return $state;
                        })
                        ->afterStateHydrated(function ($component, $state) {
                            if ($state && str_starts_with($state, 'Poli ')) {
                                $component->state(substr($state, 5));
                            }
                        }),
                    Forms\Components\TextInput::make('biaya_registrasi')
                        ->label('Biaya Pendaftaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                    Forms\Components\TextInput::make('biaya_konsultasi')
                        ->label('Biaya Konsultasi Dokter')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_poli')
                    ->label('Nama Poli')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_registrasi')
                    ->label('Reg')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('biaya_konsultasi')
                    ->label('Konsul')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('dokters_count')
                    ->counts('dokters')
                    ->label('Dokter')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            RelationManagers\DoktersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolis::route('/'),
            'create' => Pages\CreatePoli::route('/create'),
            'edit' => Pages\EditPoli::route('/{record}/edit'),
        ];
    }
}
