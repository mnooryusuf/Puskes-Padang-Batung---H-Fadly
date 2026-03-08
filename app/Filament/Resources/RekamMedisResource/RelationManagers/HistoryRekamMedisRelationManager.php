<?php

namespace App\Filament\Resources\RekamMedisResource\RelationManagers;

use App\Models\RekamMedis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HistoryRekamMedisRelationManager extends RelationManager
{
    protected static string $relationship = 'pendaftaran';
    protected static ?string $title = 'Riwayat Rekam Medis Pasien';

    public function table(Table $table): Table
    {
        // We want to show ALL rekam medis for the patient associated with this record
        $pasienId = $this->getOwnerRecord()->pendaftaran->pasien_id;

        return $table
            ->query(fn () => RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $pasienId)))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penyakit.kode')
                    ->label('ICD-10'),
                Tables\Columns\TextColumn::make('penyakit.nama_penyakit')
                    ->label('Diagnosis')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status_pulang')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // No header actions
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (RekamMedis $record): string => \App\Filament\Resources\RekamMedisResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
