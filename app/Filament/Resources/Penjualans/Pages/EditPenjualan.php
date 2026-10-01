<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
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
            DeleteAction::make()
                ->after(fn () => self::restoreUnits($this->record->id)),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['use_ppn'] = (float) ($data['ppn'] ?? 0) > 0;

        $data['items'] = DetailPenjualan::where('penjualan_id', $this->record->id)
            ->get()
            ->map(function (DetailPenjualan $detail): array {
                $unit = GudangBarang::withTrashed()->with('barang')->find($detail->gudang_barang_id);

                return [
                    'barang_id' => $detail->barang_id,
                    'jumlah' => 1,
                    'serial_numbers' => $detail->gudang_barang_id ? [$detail->gudang_barang_id] : [],
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

        self::restoreUnits($this->record->id);
        CreatePenjualan::saveItems($this->record->id, (int) $this->record->toko_id, $this->items);
    }

    public static function restoreUnits(int $penjualanId): void
    {
        foreach (DetailPenjualan::where('penjualan_id', $penjualanId)->get() as $detail) {
            GudangBarang::withTrashed()->where('id', $detail->gudang_barang_id)->restore();
        }

        DetailPenjualan::where('penjualan_id', $penjualanId)->delete();
    }
}
