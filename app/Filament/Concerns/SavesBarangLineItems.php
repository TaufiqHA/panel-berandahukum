<?php

namespace App\Filament\Concerns;

use App\Filament\Support\LineItems;
use Illuminate\Database\Eloquent\Model;

trait SavesBarangLineItems
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $lineItems = null;

    /**
     * @return class-string<Model>
     */
    abstract protected function lineItemModel(): string;

    abstract protected function lineItemForeignKey(): string;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function captureLineItems(array $data): array
    {
        if (array_key_exists('items', $data)) {
            $this->lineItems = $data['items'] ?? [];
        }

        unset($data['items']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function fillLineItems(array $data): array
    {
        $model = $this->lineItemModel();
        $foreignKey = $this->lineItemForeignKey();

        $data['items'] = $model::where($foreignKey, $this->record->id)
            ->get()
            ->map(fn ($detail): array => [
                'barang_id' => $detail->barang_id,
                'jumlah' => $detail->jumlah,
                'price' => $detail->price,
                'discount' => $detail->discount,
            ])
            ->all();

        return $data;
    }

    protected function saveLineItems(int $id): void
    {
        if ($this->lineItems === null) {
            return;
        }

        $model = $this->lineItemModel();
        $foreignKey = $this->lineItemForeignKey();

        $model::where($foreignKey, $id)->delete();

        foreach ($this->lineItems as $item) {
            $model::create([
                $foreignKey => $id,
                'barang_id' => $item['barang_id'] ?? null,
                'jumlah' => $item['jumlah'] ?? 1,
                'price' => $item['price'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'subtotal' => LineItems::lineSubtotal($item),
            ]);
        }
    }
}
