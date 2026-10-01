<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Concerns\SavesBarangLineItems;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\QuotationDetail;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(fn () => QuotationDetail::where('quotation_id', $this->record->id)->delete()),
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
            $data['subtotal'] = $subtotal;
            $data['ppn'] = $usePpn ? round($subtotal * 0.11) : 0;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveLineItems($this->record->id);
    }
}
