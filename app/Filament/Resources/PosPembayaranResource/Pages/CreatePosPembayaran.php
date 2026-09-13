<?php

namespace App\Filament\Resources\PosPembayaranResource\Pages;

use App\Filament\Resources\PosPembayaranResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosPembayaran extends CreateRecord
{
    protected static string $resource = PosPembayaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
