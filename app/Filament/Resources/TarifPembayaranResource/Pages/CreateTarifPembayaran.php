<?php

namespace App\Filament\Resources\TarifPembayaranResource\Pages;

use App\Filament\Resources\TarifPembayaranResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTarifPembayaran extends CreateRecord
{
    protected static string $resource = TarifPembayaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
