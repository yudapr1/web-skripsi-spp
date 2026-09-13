<?php

namespace App\Filament\Resources\TagihanTahunanResource\Pages;

use App\Filament\Resources\TagihanTahunanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagihanTahunan extends EditRecord
{
    protected static string $resource = TagihanTahunanResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;
        
        $totalTagihan = $record->items()->sum('nominal_pos');
        $totalTerbayar = $record->items()->sum('nominal_terbayar');
        $sisaTagihan = max(0, $totalTagihan - $totalTerbayar);
        
        $status = ($totalTagihan > 0 && $sisaTagihan == 0) ? 'lunas' : ($totalTerbayar > 0 ? 'sebagian' : 'belum_lunas');

        $record->updateQuietly([
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'sisa_tagihan' => $sisaTagihan,
            'status' => $status,
        ]);
    }

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
