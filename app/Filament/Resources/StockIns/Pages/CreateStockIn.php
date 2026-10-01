<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use App\Models\DetailBarangMasuk;
use App\Models\GudangBarang;
use Filament\Resources\Pages\CreateRecord;

class CreateStockIn extends CreateRecord
{
    protected static string $resource = StockInResource::class;

    /**
     * @var array<int, string|null>
     */
    protected array $serialNumbers = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $type = (int) ($data['type_serial_number'] ?? 2);

        if ($type === 1) {
            $this->serialNumbers = preg_split('/[\s,]+/', (string) ($data['serial_all'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        } else {
            $this->serialNumbers = collect($data['serial_items'] ?? [])
                ->pluck('serial_number')
                ->all();
        }

        unset($data['serial_all'], $data['serial_items']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $stockIn = $this->record;
        $jumlah = (int) $stockIn->jumlah;

        for ($i = 0; $i < $jumlah; $i++) {
            $gudang = GudangBarang::create([
                'barang_id' => $stockIn->barang_id,
                'serial_number_id' => $this->serialNumbers[$i] ?? null,
                'toko_id' => $stockIn->toko_id,
                'status' => 1,
            ]);

            DetailBarangMasuk::create([
                'stock_in_id' => $stockIn->id,
                'gudang_barang_id' => $gudang->id,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
