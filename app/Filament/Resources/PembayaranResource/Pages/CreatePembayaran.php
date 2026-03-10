<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Antrian;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    /**
     * Pre-fill pendaftaran_id from query parameter (from Antrian Kasir).
     */
    public function mount(): void
    {
        parent::mount();

        $pendaftaranId = request()->query('pendaftaran_id');
        if ($pendaftaranId) {
            $this->form->fill([
                'pendaftaran_id' => (int) $pendaftaranId,
            ]);
        }
    }

    /**
     * After creating a Pembayaran, auto-update Pendaftaran and Antrian statuses.
     */
    protected function afterCreate(): void
    {
        $pembayaran = $this->record;

        if ($pembayaran->status_pembayaran === 'Lunas' || $pembayaran->status_pembayaran === 'Gratis') {
            // Update Pendaftaran status
            $pembayaran->pendaftaran->update(['status' => 'Selesai']);

            // Update Antrian Kasir status
            Antrian::where('pendaftaran_id', $pembayaran->pendaftaran_id)
                ->where('kategori', 'Kasir')
                ->update(['status' => 'Selesai']);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
