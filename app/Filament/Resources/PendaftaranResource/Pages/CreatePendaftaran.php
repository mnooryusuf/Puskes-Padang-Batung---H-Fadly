<?php
namespace App\Filament\Resources\PendaftaranResource\Pages;
use App\Filament\Resources\PendaftaranResource;
use Filament\Resources\Pages\CreateRecord;
class CreatePendaftaran extends CreateRecord 
{ 
    protected static string $resource = PendaftaranResource::class; 

    protected function afterCreate(): void
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
