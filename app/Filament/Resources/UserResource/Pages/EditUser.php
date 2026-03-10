<?php
namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record;

        if ($user->role === 'dokter') {
            $data['dokter_selector'] = $user->dokter?->id;
        } elseif ($user->role === 'pasien') {
            $data['pasien_selector'] = $user->pasien?->id;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $data = $this->data;

        // Clear existing links for this user
        \App\Models\Dokter::where('user_id', $user->id)->update(['user_id' => null]);
        \App\Models\Pasien::where('user_id', $user->id)->update(['user_id' => null]);

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
