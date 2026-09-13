<?php

namespace App\Filament\Resources\TarifPembayaranResource\Pages;

use App\Filament\Resources\TarifPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTarifPembayaran extends ListRecords
{
    protected static string $resource = TarifPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Tarif Baru'),
        ];
    }
}
