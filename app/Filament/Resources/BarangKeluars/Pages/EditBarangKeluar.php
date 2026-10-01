<?php

namespace App\Filament\Resources\BarangKeluars\Pages;

use App\Filament\Resources\BarangKeluars\BarangKeluarResource;
use App\Models\DataBarangKeluar;
use App\Models\GudangBarang;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBarangKeluar extends EditRecord
{
    protected static string $resource = BarangKeluarResource::class;

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
        $data['items'] = DataBarangKeluar::where('barang_keluar_id', $this->record->id)
            ->get()
            ->map(fn (DataBarangKeluar $detail): array => [
                'barang_id' => $detail->barang_id,
                'jumlah' => 1,
                'serial_numbers' => $detail->gudang_barang_id ? [$detail->gudang_barang_id] : [],
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('items', $data)) {
            $this->items = $data['items'] ?? [];
        }

        unset($data['items']);
        $data['status'] = 1;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->items === null) {
            return;
        }

        self::restoreUnits($this->record->id);
        CreateBarangKeluar::saveItems($this->record->id, (int) $this->record->toko_id, $this->items);
    }

    public static function restoreUnits(int $barangKeluarId): void
    {
        foreach (DataBarangKeluar::where('barang_keluar_id', $barangKeluarId)->get() as $detail) {
            GudangBarang::withTrashed()->where('id', $detail->gudang_barang_id)->restore();
        }

        DataBarangKeluar::where('barang_keluar_id', $barangKeluarId)->delete();
    }
}
