<?php

namespace App\Filament\Resources\FacturaEmitidaResource\Pages;

use App\Filament\Resources\FacturaEmitidaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacturaEmitida extends EditRecord
{
    protected static string $resource = FacturaEmitidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
