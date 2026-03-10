<?php
namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $data = $this->data;

        if ($data['role'] === 'dokter' && !empty($data['dokter_selector'])) {
            $dokter = \App\Models\Dokter::find($data['dokter_selector']);
            if ($dokter) {
                $dokter->update(['user_id' => $user->id]);
            }
        } elseif ($data['role'] === 'pasien' && !empty($data['pasien_selector'])) {
            $pasien = \App\Models\Pasien::find($data['pasien_selector']);
            if ($pasien) {
                $pasien->update(['user_id' => $user->id]);
            }
        }
    }
}
