<?php

namespace App\Filament\Resources;

use App\Models\Pendaftaran;
use App\Models\Pasien;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AntrianPoliResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Antrian Poli';
    protected static ?string $pluralModelLabel = 'Antrian Poli';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Menunggu Poli')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form is primarily read-only for doctor's queue view
            TextInput::make('no_antrian')->disabled(),
            TextInput::make('tanggal_daftar')->disabled(),
            Select::make('pasien_id')
                ->relationship('pasien', 'nama_pasien')
                ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->no_rm}] {$record->nama_pasien}")
                ->disabled(),
            Select::make('poli_id')->relationship('poli', 'nama_poli')->disabled(),
            TextInput::make('status')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no_antrian')->sortable(),
            TextColumn::make('pasien.no_rm')->label('No. RM')->searchable(),
            TextColumn::make('pasien.nama_pasien')->label('Nama Pasien')->searchable()->sortable(),
            TextColumn::make('poli.nama_poli')->label('Poli')->sortable(),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Menunggu' => 'warning',
                    'Diperiksa' => 'info',
                    'Selesai' => 'success',
                    default => 'gray',
                }),
        ])
        ->defaultSort('no_antrian', 'asc')
        ->filters([
            Tables\Filters\SelectFilter::make('poli_id')
                ->relationship('poli', 'nama_poli')
                ->label('Filter Poli'),
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'Menunggu' => 'Menunggu',
                    'Diperiksa' => 'Diperiksa',
                    'Selesai' => 'Selesai',
                ])
                ->default('Menunggu'),
        ])
        ->actions([
            Tables\Actions\Action::make('panggil')
                ->label('Panggil')
                ->icon('heroicon-o-megaphone')
                ->color('warning')
                ->extraAttributes(fn (Pendaftaran $record): array => [
                    'onclick' => new \Illuminate\Support\HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var unit = 'Poli " . addslashes($record->poli?->nama_poli ?? "") . "'; var msg = new SpeechSynthesisUtterance('Nomor antrian " . addslashes($record->no_antrian) . ", silakan menuju ke ' + unit); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                ]),
            Tables\Actions\Action::make('periksa')
                ->label('Periksa')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->url(fn (Pendaftaran $record): string => RekamMedisResource::getUrl('create', ['pendaftaran_id' => $record->id]))
                ->visible(fn (Pendaftaran $record): bool => $record->status !== 'Selesai'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AntrianPoliResource\Pages\ListAntrianPolis::route('/'),
        ];
    }
}
