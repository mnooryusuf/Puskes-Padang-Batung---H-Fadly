<?php

namespace App\Filament\Resources\AntrianPoliResource\Pages;

use App\Filament\Resources\AntrianPoliResource;
use Filament\Resources\Pages\ListRecords;

class ListAntrianPolis extends ListRecords
{
    protected static string $resource = AntrianPoliResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
