<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\DetailPenjualan;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $items = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['use_ppn'] = (float) ($data['ppn'] ?? 0) > 0;

        $resolved = [];

        $data['items'] = DetailPenjualan::where('penjualan_id', $this->record->id)
            ->get()
            ->map(function (DetailPenjualan $detail) use (&$resolved): array {
                $unit = $this->record->resolveDetailUnit($detail, $resolved);

                if ($unit !== null) {
                    $resolved[] = $unit->id;
                }

                return [
                    'barang_id' => $detail->barang_id,
                    'jumlah' => 1,
                    'serial_numbers' => $unit ? [$unit->id] : [],
                    'price' => $detail->price,
                    'discount' => $detail->discount,
                ];
            })
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('items', $data)) {
            $this->items = $data['items'] ?? [];
        }

        $usePpn = (bool) ($data['use_ppn'] ?? false);
        unset($data['items'], $data['use_ppn']);

        $data = TransactionData::withBuyerDefaults($data);
        $data['status'] = 1;

        $subtotal = LineItems::sum($this->items);
        $ppn = $usePpn ? round($subtotal * 0.11) : 0;
        $total = $subtotal + $ppn;

        $data['subtotal'] = $subtotal;
        $data['ppn'] = $ppn;
        $data['total_pembayaran'] = $total;
        $data['sisa'] = ($data['payment_status'] ?? null) === 'DP'
            ? max(0, $total - (float) ($data['dp_payment'] ?? 0))
            : 0;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->items === null) {
            return;
        }

        $this->record->restoreSoldUnits();
        CreatePenjualan::saveItems($this->record->id, (int) $this->record->toko_id, $this->items);
    }
}
