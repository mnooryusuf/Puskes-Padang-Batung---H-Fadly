<?php

namespace App\Filament\Resources\RekamMedisResource\Pages;

use App\Filament\Resources\RekamMedisResource;
use App\Models\Pasien;
use App\Models\RekamMedis;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

/**
 * Level 1: Daftar pasien yang memiliki rekam medis.
 * Index utama untuk menu Rekam Medis.
 */
class ListRekamMedisPasiens extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = RekamMedisResource::class;

    protected static string $view = 'filament.resources.rekam-medis-resource.pages.list-rekam-medis-pasiens';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Rekam Medis — Pilih Pasien';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pasien::query()->whereHas('rekamMedis')
            )
            ->columns([
                TextColumn::make('no_rm')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rekam_medis_count')
                    ->label('Total Kunjungan')
                    ->counts('rekamMedis')
                    ->badge()
                    ->color('info'),
                TextColumn::make('rekam_medis_max_created_at')
                    ->label('Kunjungan Terakhir')
                    ->max('rekamMedis', 'created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_diagnosis')
                    ->label('Diagnosis Terakhir')
                    ->getStateUsing(function (Pasien $record): string {
                        $last = RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $record->id))
                            ->with('penyakit')
                            ->latest()
                            ->first();
                        if (!$last) return '-';
                        return "({$last->penyakit?->kode}) {$last->penyakit?->nama_penyakit}";
                    }),
            ])
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Rekam Medis')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('primary')
                    ->url(fn (Pasien $record): string => RekamMedisResource::getUrl('by-pasien', ['pasienId' => $record->id])),
            ]);
    }
}
