<?php

namespace App\Filament\Resources\Pos\Pages;

use App\Filament\Resources\Pos\PoResource;
use App\Models\PoDetail;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPo extends EditRecord
{
    protected static string $resource = PoResource::class;

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $items = null;

    protected bool $savingAsDraft = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(fn () => PoDetail::where('po_id', $this->record->id)->delete()),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
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
            $this->save();
        } finally {
            $this->savingAsDraft = false;
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['use_ppn'] = (float) ($data['ppn'] ?? 0) > 0;
        $data['status_dp'] = (float) ($data['po_dp'] ?? 0) > 0;
        $data['status_bayar'] = (int) ($data['status_bayar'] ?? 0) === 1;
        $data['status_terima'] = (int) ($data['status_terima'] ?? 0) === 1;

        $data['items'] = $this->record->detail_po()
            ->get()
            ->map(fn (PoDetail $detail): array => [
                'barang_id' => $detail->barang_id,
                'jumlah' => $detail->jumlah,
                'price' => $detail->price,
                'discount' => $detail->discount,
            ])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('items', $data)) {
            $this->items = $data['items'] ?? [];
        }

        return CreatePo::normalizeHeader($data, $this->items ?? [], $this->savingAsDraft ? 2 : 1);
    }

    protected function afterSave(): void
    {
        if ($this->items === null) {
            return;
        }

        PoDetail::where('po_id', $this->record->id)->delete();
        CreatePo::saveDetails($this->record->id, $this->items);
    }
}
