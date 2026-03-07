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
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Resep';
    protected static ?string $pluralModelLabel = 'Resep';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('rekam_medis_id')->relationship('rekamMedis', 'id')->getOptionLabelFromRecordUsing(fn($record) => "RM #{$record->id} - {$record->pendaftaran->pasien->nama_pasien}")->required(),
            Repeater::make('detailReseps')->relationship()->schema([
                Select::make('obat_id')->relationship('obat', 'nama_obat')->required(),
                TextInput::make('dosis')->required(),
                TextInput::make('jumlah')->numeric()->required(),
            ])->columns(3)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            TextColumn::make('rekamMedis.pendaftaran.pasien.nama_pasien')->label('Pasien')->searchable(),
            TextColumn::make('detail_reseps_count')->counts('detailReseps')->label('Jumlah Obat')->badge(),
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
