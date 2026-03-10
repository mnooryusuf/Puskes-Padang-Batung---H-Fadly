<?php

namespace App\Filament\Resources\RekamMedisResource\Pages;

use App\Filament\Resources\RekamMedisResource;
use App\Models\RekamMedis;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewRekamMedis extends ViewRecord
{
    protected static string $resource = RekamMedisResource::class;

    public function viewHistoryAction(): Action
    {
        return Action::make('viewHistory')
            ->modalHeading('Rincian Rekam Medis Lama')
            ->modalWidth('4xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->form([
                Grid::make(2)->schema([
                    TextInput::make('tanggal')->label('Tanggal Kunjungan')->disabled(),
                    TextInput::make('diagnosis_icd10')->label('Diagnosis (ICD-10)')->disabled(),
                    Textarea::make('keluhan_utama')->label('Keluhan Utama')->disabled()->rows(3),
                    Textarea::make('riwayat_penyakit_sekarang')->label('Riwayat Penyakit')->disabled()->rows(3),
                    Textarea::make('pemeriksaan_fisik')->label('Pemeriksaan Fisik (Tanda Vital)')->disabled()->rows(3),
                    TextInput::make('status_pulang')->label('Status Pulang')->disabled(),
                ]),
                Textarea::make('tindakan')->label('Tindakan Medis & Penunjang')->disabled()->rows(4),
                Textarea::make('instruksi_lab')->label('Instruksi Laboratorium')->disabled()->rows(2),
                Textarea::make('resep_obat')->label('Resep Obat (E-Resep)')->disabled()->rows(4),
            ])
            ->mountUsing(function ($form, array $arguments) {
                $record = RekamMedis::with(['penyakit', 'tindakans', 'resep.detailReseps.obat'])->find($arguments['recordId'] ?? null);
                if (!$record) return;

                $diagnosisName = $record->nama_penyakit ?? $record->penyakit?->nama_penyakit;

                // Format tindakan (hanya dari pivot)
                $tindakanText = 'Tidak ada tindakan.';
                if ($record->tindakans->isNotEmpty()) {
                    $tindakanText = $record->tindakans->map(fn($t) => "• {$t->nama_tindakan} (" . ($t->pivot->jumlah ?? 1) . "x)")->implode("\n");
                }

                $form->fill([
                    'tanggal' => $record->created_at->format('d/m/Y H:i'),
                    'diagnosis_icd10' => "({$record->penyakit?->kode}) {$diagnosisName}",
                    'keluhan_utama' => $record->keluhan_utama,
                    'riwayat_penyakit_sekarang' => $record->riwayat_penyakit_sekarang,
                    'pemeriksaan_fisik' => "BB: {$record->berat_badan}kg, TD: {$record->tekanan_darah}mmHg, Suhu: {$record->suhu_tubuh}°C, Nadi: {$record->nadi}x, Resp: {$record->respirasi}x",
                    'tindakan' => $tindakanText,
                    'instruksi_lab' => $record->instruksi_lab ?: 'Tidak ada instruksi lab.',
                    'status_pulang' => $record->status_pulang,
                    'resep_obat' => $record->resep?->detailReseps?->map(fn($d) => "- {$d->obat?->nama_obat} ({$d->dosis}) : {$d->jumlah} {$d->obat?->satuan}")->implode("\n") ?: 'Tidak ada resep.',
                ]);
            });
    }
}
