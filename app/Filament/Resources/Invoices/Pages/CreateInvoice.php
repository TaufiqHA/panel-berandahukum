<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Concerns\SavesBarangLineItems;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\InvoiceDetail;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    use SavesBarangLineItems;

    protected static string $resource = InvoiceResource::class;

    protected function lineItemModel(): string
    {
        return InvoiceDetail::class;
    }

    protected function lineItemForeignKey(): string
    {
        return 'invoice_id';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->captureLineItems($data);
        $usePpn = (bool) ($data['use_ppn'] ?? false);
        unset($data['use_ppn']);

        $data = TransactionData::withBuyerDefaults($data);
        $subtotal = LineItems::sum($this->lineItems);
        $ppn = $usePpn ? round($subtotal * 0.11) : 0;
        $total = $subtotal + $ppn;

        $data['subtotal'] = $subtotal;
        $data['ppn'] = $ppn;
        $data['total_pembayaran'] = $total;
        $data['sisa'] = ($data['payment_status'] ?? null) === 'DP'
            ? max(0, $total - (float) ($data['dp_payment'] ?? 0))
            : 0;
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
