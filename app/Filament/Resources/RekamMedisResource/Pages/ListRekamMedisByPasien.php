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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

/**
 * Level 2: Daftar Rekam Medis milik satu pasien tertentu.
 */
class ListRekamMedisByPasien extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = RekamMedisResource::class;

    protected static string $view = 'filament.resources.rekam-medis-resource.pages.list-rekam-medis-by-pasien';

    public int $pasienId;
    public ?Pasien $pasien = null;

    public function mount(int $pasienId): void
    {
        $this->pasienId = $pasienId;
        $this->pasien = Pasien::find($pasienId);
    }

    public function getTitle(): string
    {
        return 'Rekam Medis — ' . ($this->pasien?->nama_pasien ?? 'Pasien');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('← Daftar Pasien')
                ->color('gray')
                ->url(RekamMedisResource::getUrl('index')),
            Action::make('tambah')
                ->label('Tambah Rekam Medis')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(fn (): string => RekamMedisResource::getUrl('create', [
                    'pendaftaran_id' => \App\Models\Pendaftaran::where('pasien_id', $this->pasienId)->latest()->first()?->id
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $this->pasienId))
                    ->latest();
            })
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('penyakit.kode')
                    ->label('ICD-10'),
                TextColumn::make('penyakit.nama_penyakit')
                    ->label('Diagnosis')
                    ->limit(40),
                TextColumn::make('dokter.nama_dokter')
                    ->label('Dokter'),
                TextColumn::make('status_pulang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sembuh' => 'success',
                        'Kontrol' => 'warning',
                        'Rujuk' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('pendaftaran.poli.nama_poli')
                    ->label('Poli'),
            ])
            ->actions([
                ViewAction::make()->url(fn (RekamMedis $record): string => RekamMedisResource::getUrl('view', ['record' => $record])),
                EditAction::make()->url(fn (RekamMedis $record): string => RekamMedisResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
