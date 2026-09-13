<?php

namespace App\Filament\Resources\TarifPembayaranResource\Pages;

use App\Filament\Resources\TarifPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTarifPembayaran extends EditRecord
{
    protected static string $resource = TarifPembayaranResource::class;

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
