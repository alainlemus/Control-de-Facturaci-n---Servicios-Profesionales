<?php

namespace App\Filament\Resources\FacturaEmitidaResource\Pages;

use App\Filament\Resources\FacturaEmitidaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacturaEmitidas extends ListRecords
{
    protected static string $resource = FacturaEmitidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Agregar Factura'),
        ];
    }
}
