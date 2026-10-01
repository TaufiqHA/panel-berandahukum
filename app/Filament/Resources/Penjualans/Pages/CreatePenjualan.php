<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use Filament\Resources\Pages\CreateRecord;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $items = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->items = $data['items'] ?? [];
        $usePpn = (bool) ($data['use_ppn'] ?? false);

        unset($data['items'], $data['use_ppn']);

        $data = TransactionData::withBuyerDefaults($data);
        $data['status'] = 1;

        return $this->applyTotals($data, $usePpn);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function applyTotals(array $data, bool $usePpn): array
    {
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

    protected function afterCreate(): void
    {
        self::saveItems($this->record->id, (int) $this->record->toko_id, $this->items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function saveItems(int $penjualanId, int $tokoId, array $items): void
    {
        foreach ($items as $item) {
            $barangId = $item['barang_id'] ?? null;
            $serials = array_values(array_filter((array) ($item['serial_numbers'] ?? [])));

            $units = $serials !== []
                ? GudangBarang::whereIn('id', $serials)->get()
                : GudangBarang::where('barang_id', $barangId)
                    ->where('toko_id', $tokoId)
                    ->where('status', 1)
                    ->limit((int) ($item['jumlah'] ?? 0))
                    ->get();

            foreach ($units as $unit) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualanId,
                    'gudang_barang_id' => $unit->id,
                    'serial_number_id' => is_numeric($unit->serial_number_id) ? (int) $unit->serial_number_id : null,
                    'barang_id' => $unit->barang_id,
                    'discount' => $item['discount'] ?? 0,
                    'price' => $item['price'] ?? 0,
                ]);

                $unit->delete();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
