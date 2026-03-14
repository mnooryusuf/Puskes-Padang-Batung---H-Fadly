<?php

namespace App\Filament\Resources;

use App\Models\Obat;
use App\Filament\Resources\ObatResource\RelationManagers;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput as FormsTextInput;
use UnitEnum;

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Obat';

    protected static ?int $navigationSort = 13;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('apoteker') || auth()->user()?->hasRole('kepala');
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

    protected static ?string $pluralModelLabel = 'Obat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_obat')->required(),
            Select::make('sediaan')
                ->options([
                    'Tablet' => 'Tablet',
                    'Kapsul' => 'Kapsul',
                    'Kaplet' => 'Kaplet',
                    'Sirup' => 'Sirup',
                    'Drop' => 'Drop',
                    'Salep' => 'Salep',
                    'Krim' => 'Krim',
                    'Injeksi' => 'Injeksi',
                    'Infus' => 'Infus',
                    'Lainnya' => 'Lainnya',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    $mapping = [
                        'Tablet' => 'Tablet',
                        'Kapsul' => 'Kapsul',
                        'Kaplet' => 'Kaplet',
                        'Sirup' => 'Botol',
                        'Drop' => 'Botol',
                        'Salep' => 'Tube',
                        'Krim' => 'Tube',
                        'Injeksi' => 'Ampul',
                        'Infus' => 'Plabottle',
                    ];
                    if (isset($mapping[$state])) {
                        $set('satuan', $mapping[$state]);
                    }
                }),
            TextInput::make('kemasan')
                ->placeholder('Contoh: Box isi 100, Botol 60ml')
                ->required(),
            TextInput::make('satuan')
                ->required()
                ->placeholder('Contoh: Tablet / Botol / Strip')
                ->helperText('Satuan terkecil yang diberikan ke pasien'),
            TextInput::make('stok')
                ->numeric()
                ->required()
                ->placeholder('Masukkan jumlah stok terkecil')
                ->helperText('Jumlah total dalam unit satuan terkecil (Contoh: Total Tablet)'),
            TextInput::make('harga_jual')
                ->numeric()
                ->prefix('Rp')
                ->required()
                ->helperText('Harga per 1 unit satuan terkecil'),
            TextInput::make('stok_minimum')
                ->numeric()
                ->default(10)
                ->required()
                ->helperText('Batas stok untuk peringatan ketersediaan'),
            \Filament\Forms\Components\DatePicker::make('expired_at')->label('Tgl Kadaluwarsa'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama_obat')->searchable()->sortable(),
            TextColumn::make('sediaan')->badge()->color('gray'),
            TextColumn::make('kemasan')->searchable(),
            TextColumn::make('satuan'),
            TextColumn::make('stok')
                ->badge()
                ->color(fn($record) => $record->stok <= $record->stok_minimum ? 'danger' : ($record->stok <= $record->stok_minimum * 2 ? 'warning' : 'success'))
                ->description(fn($record) => "Min: {$record->stok_minimum}"),
            TextColumn::make('expired_at')
                ->label('Expired')
                ->date()
                ->badge()
                ->color(fn ($state) => $state && $state->isPast() ? 'danger' : ($state && $state->diffInDays(now()) < 30 ? 'warning' : 'success'))
                ->sortable(),
            TextColumn::make('harga_jual')->label('Harga')->money('idr')->sortable(),
        ])->actions([
            Action::make('tambah_stok')
                ->label('Tambah Stok')
                ->icon('heroicon-m-plus-circle')
                ->color('success')
                ->visible(fn() => !auth()->user()->hasRole('kepala'))
                ->form([
                    FormsTextInput::make('jumlah')
                        ->label('Jumlah Tambahan')
                        ->numeric()
                        ->required()
                        ->helperText('Gunakan angka positif untuk menambah, atau negatif jika ada pengurangan manual (koreksi).'),
                    FormsTextInput::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Contoh: Barang baru masuk / Hibah')
                        ->required(),
                ])
                ->action(function (Obat $record, array $data) {
                    $record->increment('stok', $data['jumlah']);
                    
                    // Riwayat stok otomatis tercatat melalui ObatObserver
                    // Update deskripsi pada entri terakhir agar sesuai dengan input pengguna
                    $record->stockHistories()->latest()->limit(1)->update([
                        'description' => $data['keterangan']
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Stok Berhasil Ditambahkan')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StockHistoriesRelationManager::class,
        ];
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
