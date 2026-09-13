<?php

namespace App\Filament\Resources\PosPembayaranResource\Pages;

use App\Filament\Resources\PosPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPosPembayaran extends EditRecord
{
    protected static string $resource = PosPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
