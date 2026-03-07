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

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Pendaftaran';
    protected static ?string $pluralModelLabel = 'Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('pasien_id')->relationship('pasien', 'nama_pasien')->searchable()->preload()->createOptionForm([
                TextInput::make('no_rm')->required(),
                TextInput::make('nama_pasien')->required(),
                DatePicker::make('tanggal_lahir')->required(),
                Select::make('jenis_kelamin')->options(['L'=>'L','P'=>'P'])->required(),
                TextInput::make('no_hp')->required(),
                TextInput::make('alamat')->required(),
            ])->required(),
            DatePicker::make('tanggal_daftar')->default(now())->required(),
            Select::make('poli')->options(['Poli Umum' => 'Poli Umum', 'Poli Gigi' => 'Poli Gigi', 'Poli KIA' => 'Poli KIA'])->required(),
            TextInput::make('no_antrian')->numeric()->required()->default(fn() => Pendaftaran::where('tanggal_daftar', now()->toDateString())->count() + 1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no_antrian')->sortable(),
            TextColumn::make('tanggal_daftar')->date()->sortable(),
            TextColumn::make('pasien.nama_pasien')->searchable()->sortable(),
            TextColumn::make('poli')->sortable(),
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
            'index' => PendaftaranResource\Pages\ListPendaftarans::route('/'),
            'create' => PendaftaranResource\Pages\CreatePendaftaran::route('/create'),
            'edit' => PendaftaranResource\Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
