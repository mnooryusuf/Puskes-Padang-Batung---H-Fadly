<?php

namespace App\Filament\Resources\ObatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'stockHistories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->color(fn($state) => match($state){'Masuk'=>'success','Keluar'=>'danger','Penyesuaian'=>'warning'}),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah'),
                Tables\Columns\TextColumn::make('stock_after')->label('Stok Akhir'),
                Tables\Columns\TextColumn::make('description')->label('Keterangan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Read Only for history
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
