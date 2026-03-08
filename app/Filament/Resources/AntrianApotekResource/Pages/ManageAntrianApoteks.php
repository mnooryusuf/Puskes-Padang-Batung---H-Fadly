<?php

namespace App\Filament\Resources\AntrianApotekResource\Pages;

use App\Filament\Resources\AntrianApotekResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAntrianApoteks extends ManageRecords
{
    protected static string $resource = AntrianApotekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
