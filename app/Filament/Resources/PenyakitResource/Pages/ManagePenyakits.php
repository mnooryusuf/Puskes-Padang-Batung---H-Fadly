<?php

namespace App\Filament\Resources\PenyakitResource\Pages;

use App\Filament\Resources\PenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePenyakits extends ManageRecords
{
    protected static string $resource = PenyakitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
