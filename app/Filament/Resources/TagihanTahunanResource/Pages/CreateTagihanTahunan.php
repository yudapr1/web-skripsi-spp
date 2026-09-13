<?php

namespace App\Filament\Resources\TagihanTahunanResource\Pages;

use App\Filament\Resources\TagihanTahunanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTagihanTahunan extends CreateRecord
{
    protected static string $resource = TagihanTahunanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_tagihan'] = 0.00;
        $data['total_terbayar'] = 0.00;
        $data['sisa_tagihan'] = 0.00;
        $data['status'] = 'belum_lunas';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        $totalTagihan = $record->items()->sum('nominal_pos');
        $totalTerbayar = $record->items()->sum('nominal_terbayar');
        $sisaTagihan = max(0, $totalTagihan - $totalTerbayar);
        
        // Status tagihan: lunas jika total tagihan > 0 dan sisa 0, atau sebagian jika terbayar > 0, selain itu belum_lunas
        $status = ($totalTagihan > 0 && $sisaTagihan == 0) ? 'lunas' : ($totalTerbayar > 0 ? 'sebagian' : 'belum_lunas');

        $record->updateQuietly([
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'sisa_tagihan' => $sisaTagihan,
            'status' => $status,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
