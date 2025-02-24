<?php

namespace App\Filament\Resources\FacturaRecibidaResource\Pages;

use App\Filament\Resources\FacturaRecibidaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacturaRecibida extends EditRecord
{
    protected static string $resource = FacturaRecibidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
