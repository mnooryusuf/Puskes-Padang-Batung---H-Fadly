<?php

namespace App\Filament\Pasien\Resources\AntrianAktifResource\Pages;

use App\Filament\Pasien\Resources\AntrianAktifResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAntrianAktifs extends ManageRecords
{
    protected static string $resource = AntrianAktifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No creations allowed for patients here
        ];
    }
}
