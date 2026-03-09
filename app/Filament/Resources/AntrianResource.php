<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianResource\Pages;
use App\Filament\Resources\AntrianResource\RelationManagers;
use App\Models\Antrian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AntrianResource extends Resource
{
    protected static ?string $model = Antrian::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Antrian';
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
    protected static ?string $pluralModelLabel = 'Antrian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Antrian')->schema([
                    Forms\Components\Select::make('pendaftaran_id')
                        ->relationship('pendaftaran', 'id')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->pasien->nama_pasien} (#{$record->no_antrian})")
                        ->required(),
                    Forms\Components\Select::make('kategori')
                        ->options([
                            'Poli' => 'Poli',
                            'Obat' => 'Apotek (Obat)',
                            'Kasir' => 'Kasir',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('nomor_antrian')
                        ->required()
                        ->readonly(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Menunggu' => 'Menunggu',
                            'Dipanggil' => 'Dipanggil',
                            'Selesai' => 'Selesai',
                        ])
                        ->required(),
                    Forms\Components\Select::make('poli_id')
                        ->relationship('poli', 'nama_poli')
                        ->label('Poli'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_antrian')
                    ->label('No. Antrian')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Pasien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Poli' => 'info',
                        'Obat' => 'warning',
                        'Kasir' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('poli.nama_poli')
                    ->label('Unit/Poli')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Dipanggil' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->time()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'Poli' => 'Poli',
                        'Obat' => 'Obat',
                        'Kasir' => 'Kasir',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Dipanggil' => 'Dipanggil',
                        'Selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('panggil')
                    ->label('Panggil')
                    ->icon('heroicon-o-megaphone')
                    ->color('warning')
                    ->extraAttributes(fn (Antrian $record): array => [
                        'onclick' => new \Illuminate\Support\HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var unit = '" . addslashes($record->kategori === 'Poli' ? 'Poli ' . ($record->poli?->nama_poli ?? '') : ($record->kategori === 'Obat' ? 'Apotek' : $record->kategori)) . "'; var msg = new SpeechSynthesisUtterance('Nomor antrian " . addslashes($record->nomor_antrian) . ", silakan menuju ke ' + unit); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                    ])
                    ->action(fn (Antrian $record) => $record->update(['status' => 'Dipanggil']))
                    ->visible(fn (Antrian $record) => $record->status === 'Menunggu'),
                Tables\Actions\Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Antrian $record) => $record->update(['status' => 'Selesai']))
                    ->visible(fn (Antrian $record) => $record->status === 'Dipanggil'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('10s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAntrians::route('/'),
            'create' => Pages\CreateAntrian::route('/create'),
            'edit' => Pages\EditAntrian::route('/{record}/edit'),
        ];
    }
}
