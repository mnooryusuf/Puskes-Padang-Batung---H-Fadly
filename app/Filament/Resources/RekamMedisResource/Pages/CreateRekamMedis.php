<?php

namespace App\Filament\Resources\RekamMedisResource\Pages;

use App\Filament\Resources\RekamMedisResource;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;

class CreateRekamMedis extends CreateRecord
{
    protected static string $resource = RekamMedisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewHistory')
                ->modalHeading('Rincian Rekam Medis Lama')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->form([
                    \Filament\Forms\Components\Grid::make(2)->schema([
                        TextInput::make('tanggal')->label('Tanggal Kunjungan')->disabled(),
                        TextInput::make('diagnosis_icd10')->label('Diagnosis (ICD-10)')->disabled(),
                        Textarea::make('keluhan_utama')->label('Keluhan Utama')->disabled()->rows(3),
                        Textarea::make('riwayat_penyakit_sekarang')->label('Riwayat Penyakit')->disabled()->rows(3),
                        Textarea::make('pemeriksaan_fisik')->label('Pemeriksaan Fisik (Tanda Vital)')->disabled()->rows(3),
                        Textarea::make('tindakan')->label('Tindakan Medis')->disabled()->rows(3),
                    ]),
                    Textarea::make('resep_obat')
                        ->label('Resep Obat (E-Resep)')
                        ->disabled()
                        ->rows(4),
                ])
                ->mountUsing(function ($form, array $arguments) {
                    $record = RekamMedis::find($arguments['recordId'] ?? null);
                    if (!$record) return;

                    $diagnosisName = $record->nama_penyakit ?? $record->penyakit?->nama_penyakit;

                    $form->fill([
                        'tanggal' => $record->created_at->format('d/m/Y H:i'),
                        'diagnosis_icd10' => "({$record->penyakit?->kode}) {$diagnosisName}",
                        'keluhan_utama' => $record->keluhan_utama,
                        'riwayat_penyakit_sekarang' => $record->riwayat_penyakit_sekarang,
                        'pemeriksaan_fisik' => "BB: {$record->berat_badan}kg, TD: {$record->tekanan_darah}mmHg, Suhu: {$record->suhu_tubuh}°C, Nadi: {$record->nadi}x, Resp: {$record->respirasi}x",
                        'tindakan' => $record->tindakan,
                        'resep_obat' => $record->resep?->detailReseps?->map(fn($d) => "- {$d->obat?->nama_obat} ({$d->dosis}) : {$d->jumlah} {$d->obat?->satuan}")->implode("\n") ?: 'Tidak ada resep.',
                    ]);
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $pendaftaran = $record->pendaftaran;

        if ($pendaftaran) {
            // Check if there is a prescription (saved via repeater)
            // In afterCreate, relationship should be already processed by saveRelationships()
            $hasResep = $record->resep()->exists();
            
            if ($hasResep) {
                $pendaftaran->update(['status' => 'Menunggu Obat']);
                // Antrian Obat is already created by Resep model's booted event
            } else {
                $pendaftaran->update(['status' => 'Menunggu Pembayaran']);
                
                // Create Antrian Kasir record since no Resep will trigger it
                \App\Models\Antrian::firstOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'kategori' => 'Kasir',
                    ],
                    [
                        'nomor_antrian' => \App\Models\Antrian::generateNomor('Kasir'),
                        'status' => 'Menunggu',
                    ]
                );
            }
        }
    }

    public function mount(): void
    {
        parent::mount();

        $pendaftaranId = request()->query('pendaftaran_id');

        if ($pendaftaranId) {
            $pendaftaran = Pendaftaran::find($pendaftaranId);
            if ($pendaftaran) {
                $this->form->fill([
                    'pendaftaran_id' => $pendaftaran->id,
                    'dokter_id' => $pendaftaran->poli?->dokter?->id, // Attempt to auto-fill doctor from poli
                ]);

                // Update status to 'Pemeriksaan' when doctor opens the form
                $pendaftaran->update(['status' => 'Pemeriksaan']);
            }
        }
    }
}
