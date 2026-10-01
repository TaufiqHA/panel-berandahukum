<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Concerns\SavesBarangLineItems;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\QuotationDetail;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    use SavesBarangLineItems;

    protected static string $resource = QuotationResource::class;

    protected function lineItemModel(): string
    {
        return QuotationDetail::class;
    }

    protected function lineItemForeignKey(): string
    {
        return 'quotation_id';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->captureLineItems($data);
        $usePpn = (bool) ($data['use_ppn'] ?? false);
        unset($data['use_ppn']);

        $data = TransactionData::withBuyerDefaults($data);
        $subtotal = LineItems::sum($this->lineItems);

        $data['subtotal'] = $subtotal;
        $data['ppn'] = $usePpn ? round($subtotal * 0.11) : 0;
        $data['status'] = 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveLineItems($this->record->id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
