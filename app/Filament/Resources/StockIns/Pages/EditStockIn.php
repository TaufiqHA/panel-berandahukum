<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use App\Models\DetailBarangMasuk;
use App\Models\GudangBarang;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockIn extends EditRecord
{
    protected static string $resource = StockInResource::class;

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $serialRows = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (): void {
                    self::deleteStockUnits($this->record->id);
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['type_serial_number'] = (int) ($this->record->type_serial_number ?: 2);
        $data['serial_items'] = $this->record->detail_barang_masuk()
            ->with('gudang_barang')
            ->get()
            ->map(fn (DetailBarangMasuk $detail): array => [
                'gudang_barang_id' => $detail->gudang_barang_id,
                'serial_number' => $detail->gudang_barang?->serial_number_id,
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('serial_items', $data)) {
            $this->serialRows = $data['serial_items'] ?? [];
        }

        unset($data['serial_all'], $data['serial_items']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->serialRows === null) {
            return;
        }

        $stockIn = $this->record;
        $keptIds = [];

        foreach ($this->serialRows as $row) {
            $gudangId = $row['gudang_barang_id'] ?? null;

            if ($gudangId && $gudang = GudangBarang::find($gudangId)) {
                $gudang->update([
                    'serial_number_id' => $row['serial_number'] ?? null,
                    'barang_id' => $stockIn->barang_id,
                    'toko_id' => $stockIn->toko_id,
                ]);
                $keptIds[] = $gudang->id;

                continue;
            }

            $gudang = GudangBarang::create([
                'barang_id' => $stockIn->barang_id,
                'serial_number_id' => $row['serial_number'] ?? null,
                'toko_id' => $stockIn->toko_id,
                'status' => 1,
            ]);

            DetailBarangMasuk::create([
                'stock_in_id' => $stockIn->id,
                'gudang_barang_id' => $gudang->id,
            ]);

            $keptIds[] = $gudang->id;
        }

        $removed = DetailBarangMasuk::where('stock_in_id', $stockIn->id)
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('gudang_barang_id', $keptIds))
            ->get();

        foreach ($removed as $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->forceDelete();
            $detail->forceDelete();
        }

        if ($keptIds !== []) {
            GudangBarang::whereIn('id', $keptIds)->update([
                'barang_id' => $stockIn->barang_id,
                'toko_id' => $stockIn->toko_id,
            ]);
        }
    }

    public static function deleteStockUnits(int $stockInId): void
    {
        $gudangIds = DetailBarangMasuk::where('stock_in_id', $stockInId)->pluck('gudang_barang_id');

        GudangBarang::whereIn('id', $gudangIds)->delete();
        DetailBarangMasuk::where('stock_in_id', $stockInId)->delete();
    }
}
