<?php

namespace App\Filament\Resources\AntrianKasirResource\Pages;

use App\Filament\Resources\AntrianKasirResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAntrianKasirs extends ManageRecords
{
    protected static string $resource = AntrianKasirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
