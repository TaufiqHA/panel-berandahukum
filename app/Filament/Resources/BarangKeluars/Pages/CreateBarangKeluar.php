<?php

namespace App\Filament\Resources\BarangKeluars\Pages;

use App\Filament\Resources\BarangKeluars\BarangKeluarResource;
use App\Models\DataBarangKeluar;
use App\Models\GudangBarang;
use Filament\Resources\Pages\CreateRecord;

class CreateBarangKeluar extends CreateRecord
{
    protected static string $resource = BarangKeluarResource::class;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $items = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->items = $data['items'] ?? [];
        unset($data['items']);

        $data['status'] = 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        self::saveItems($this->record->id, (int) $this->record->toko_id, $this->items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function saveItems(int $barangKeluarId, int $tokoId, array $items): void
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
                DataBarangKeluar::create([
                    'barang_keluar_id' => $barangKeluarId,
                    'gudang_barang_id' => $unit->id,
                    'barang_id' => $unit->barang_id,
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
