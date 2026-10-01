<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Concerns\SavesBarangLineItems;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\InvoiceDetail;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(fn () => InvoiceDetail::where('invoice_id', $this->record->id)->delete()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['use_ppn'] = (float) ($data['ppn'] ?? 0) > 0;

        return $this->fillLineItems($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->captureLineItems($data);
        $usePpn = (bool) ($data['use_ppn'] ?? false);
        unset($data['use_ppn']);

        $data = TransactionData::withBuyerDefaults($data);

        if ($this->lineItems !== null) {
            $subtotal = LineItems::sum($this->lineItems);
            $ppn = $usePpn ? round($subtotal * 0.11) : 0;
            $total = $subtotal + $ppn;

            $data['subtotal'] = $subtotal;
            $data['ppn'] = $ppn;
            $data['total_pembayaran'] = $total;
            $data['sisa'] = ($data['payment_status'] ?? null) === 'DP'
                ? max(0, $total - (float) ($data['dp_payment'] ?? 0))
                : 0;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveLineItems($this->record->id);
    }
}
