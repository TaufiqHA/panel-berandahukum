<?php

namespace App\Filament\Resources\Pos\Pages;

use App\Filament\Resources\Pos\PoResource;
use App\Filament\Support\LineItems;
use App\Models\PoDetail;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePo extends CreateRecord
{
    protected static string $resource = PoResource::class;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $items = [];

    protected bool $savingAsDraft = false;

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('saveDraft')
                ->label('Draft')
                ->color('danger')
                ->action('saveDraft'),
        ];
    }

    public function saveDraft(): void
    {
        $this->savingAsDraft = true;

        try {
            $this->create();
        } finally {
            $this->savingAsDraft = false;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->items = $data['items'] ?? [];
        $data = self::normalizeHeader($data, $this->items, $this->savingAsDraft ? 2 : 1);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    public static function normalizeHeader(array $data, array $items, int $status = 1): array
    {
        $usePpn = (bool) ($data['use_ppn'] ?? false);
        $isDp = (bool) ($data['status_dp'] ?? false);

        unset($data['items'], $data['use_ppn'], $data['status_dp']);

        $subtotal = LineItems::sum($items);

        $data['status'] = $status;
        $data['status_bayar'] = (bool) ($data['status_bayar'] ?? false) ? 1 : 0;
        $data['status_terima'] = (bool) ($data['status_terima'] ?? false) ? 1 : 0;
        $data['subtotal'] = $subtotal;
        $data['ppn'] = $usePpn ? round($subtotal * 0.11) : 0;

        if (! $isDp) {
            $data['po_dp'] = 0;
        }

        return self::applySupplierFields($data);
    }

    protected function afterCreate(): void
    {
        self::saveDetails($this->record->id, $this->items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function saveDetails(int $poId, array $items): void
    {
        foreach ($items as $item) {
            PoDetail::create([
                'po_id' => $poId,
                'barang_id' => $item['barang_id'] ?? null,
                'jumlah' => $item['jumlah'] ?? 1,
                'price' => $item['price'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'subtotal' => LineItems::lineSubtotal($item),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applySupplierFields(array $data): array
    {
        if (! empty($data['supplier_id'])) {
            $supplier = Supplier::find($data['supplier_id']);

            if ($supplier) {
                $data['nama_supplier'] = $supplier->nama_supplier;
                $data['alamat_supplier'] = $supplier->alamat;
                $data['nama_sales'] = $supplier->sales;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
