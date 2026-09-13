<?php

namespace App\Filament\Resources\PosPembayaranResource\Pages;

use App\Filament\Resources\PosPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosPembayaran extends ListRecords
{
    protected static string $resource = PosPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Pos Pembayaran'),
        ];
    }
}
