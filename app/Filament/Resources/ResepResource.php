<?php

namespace App\Filament\Resources;

use App\Models\Resep;
use App\Models\Obat;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ResepResource extends Resource
{
    protected static ?string $model = Resep::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Data Resep';
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
    protected static ?string $pluralModelLabel = 'Data Resep';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('rekam_medis_id')->relationship('rekamMedis', 'id')->getOptionLabelFromRecordUsing(fn($record) => "{$record->pendaftaran->pasien->no_rm} - {$record->pendaftaran->pasien->nama_pasien}")->searchable()->preload()->required(),
            Repeater::make('detailReseps')->relationship()->schema([
                Select::make('obat_id')
                    ->relationship('obat', 'nama_obat')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $obat = \App\Models\Obat::find($state);
                        $obat = Obat::find($state);
                        $set('satuan_view', $obat?->satuan);
                    }),
                TextInput::make('satuan_view')
                    ->label('Satuan')
                    ->disabled()
                    ->dehydrated(false)
                    ->default(function ($get) {
                        $obatId = $get('obat_id');
                        if ($obatId) {
                            $obat = Obat::find($obatId);
                            return $obat ? $obat->satuan : null; // Changed to return satuan, not harga
                        }
                        return null;
                    }),
                TextInput::make('dosis')->required(),
                TextInput::make('jumlah')->numeric()->required(),
            ])->columns(4)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            TextColumn::make('rekamMedis.pendaftaran.pasien.no_rm')
                ->label('No. RM')
                ->searchable()
                ->sortable(),
            TextColumn::make('rekamMedis.pendaftaran.pasien.nama_pasien')->label('Pasien')->searchable(),
            TextColumn::make('status_pengambilan')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Menunggu' => 'gray',
                    'Diproses' => 'warning',
                    'Siap Diambil' => 'info',
                    'Sudah Diserahkan' => 'success',
                    default => 'gray',
                }),
            TextColumn::make('detail_reseps_count')->counts('detailReseps')->label('Jml Item'),
        ])->actions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ResepResource\Pages\ListReseps::route('/'),
            'create' => ResepResource\Pages\CreateResep::route('/create'),
            'edit' => ResepResource\Pages\EditResep::route('/{record}/edit'),
        ];
    }
}
