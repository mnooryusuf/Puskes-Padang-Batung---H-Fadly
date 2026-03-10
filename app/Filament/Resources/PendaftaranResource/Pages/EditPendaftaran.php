<?php
namespace App\Filament\Resources\PendaftaranResource\Pages;
use App\Filament\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPendaftaran extends EditRecord {
    protected static string $resource = PendaftaranResource::class;
    
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }

    protected function afterSave(): void
    {
        $pasien = $this->record->pasien;
        $noBpjs = $this->data['no_bpjs'] ?? null;

        if ($pasien && $noBpjs) {
            $pasien->update([
                'no_bpjs' => $noBpjs,
                'cara_bayar' => 'BPJS',
            ]);
        }
    }
}
