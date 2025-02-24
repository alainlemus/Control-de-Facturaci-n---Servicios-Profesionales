<?php

namespace App\Filament\Resources\FacturaRecibidaResource\Pages;

use App\Filament\Resources\FacturaRecibidaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacturaRecibidas extends ListRecords
{
    protected static string $resource = FacturaRecibidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
