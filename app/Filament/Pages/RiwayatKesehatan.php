<?php

namespace App\Filament\Pages;

use App\Models\RekamMedis;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class RiwayatKesehatan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationLabel = 'Riwayat Kesehatan';
    protected static ?string $title = 'Riwayat Kesehatan';
    protected static ?string $navigationGroup = 'Layanan Pasien';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('pasien');
    }

    protected static string $view = 'filament.pages.riwayat-kesehatan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RekamMedis::query()
                    ->whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', auth()->user()->pasien?->id))
                    ->latest()
            )
            ->columns([
                TextColumn::make('pendaftaran.tanggal_daftar')
                    ->label('Tanggal Berobat')
                    ->date()
                    ->sortable(),
                TextColumn::make('pendaftaran.poli.nama_poli')
                    ->label('Poli')
                    ->badge()
                    ->color('info'),
                TextColumn::make('dokter.nama_dokter')
                    ->label('Dokter')
                    ->placeholder('N/A'),
                TextColumn::make('penyakit.nama_penyakit')
                    ->label('Diagnosa (ICD-10)')
                    ->placeholder('Belum ada diagnosa'),
                TextColumn::make('diagnosa')
                    ->label('Keterangan Diagnosa')
                    ->limit(30)
                    ->placeholder('-'),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Detail Pemeriksaan')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Detail Rekam Medis')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (RekamMedis $record) => view(
                        'filament.components.riwayat-detail',
                        ['record' => $record]
                    )),
                Action::make('view_resep')
                    ->label('Resep Obat')
                    ->icon('heroicon-m-beaker')
                    ->color('success')
                    ->modalHeading('Daftar Obat & Aturan Pakai')
                    ->modalSubmitAction(false)
                    ->visible(fn (RekamMedis $record) => $record->resep()->exists())
                    ->modalContent(fn (RekamMedis $record) => view(
                        'filament.components.resep-detail',
                        ['resep' => $record->resep]
                    )),
            ])
            ->emptyStateHeading('Belum ada riwayat berobat')
            ->emptyStateDescription('Riwayat pemeriksaan Anda akan muncul di sini setelah Anda berkunjung ke Puskesmas.');
    }
}
