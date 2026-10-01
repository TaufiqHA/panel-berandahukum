<?php

namespace App\Filament\Resources\PindahGudangs\Pages;

use App\Filament\Resources\PindahGudangs\PindahGudangResource;
use App\Models\DetailBarangKeluar;
use App\Models\GudangBarang;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPindahGudang extends EditRecord
{
    protected static string $resource = PindahGudangResource::class;

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $items = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('terima')
                ->label('Terima')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => (int) $this->record->status !== 2)
                ->requiresConfirmation()
                ->action(function () {
                    PindahGudangResource::receive($this->record->id);

                    return redirect(PindahGudangResource::getUrl('index'));
                }),
            DeleteAction::make()
                ->after(fn () => PindahGudangResource::revertAndDelete($this->record->id)),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = DetailBarangKeluar::where('pindah_gudang_id', $this->record->id)
            ->get()
            ->map(fn (DetailBarangKeluar $detail): array => [
                'gudang_barang_id' => $detail->gudang_barang_id,
                'keterangan' => $detail->keterangan,
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

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->items === null) {
            return;
        }

        $transferId = $this->record->id;
        $keptIds = [];

        foreach ($this->items as $row) {
            $gudangId = $row['gudang_barang_id'] ?? null;

            if (! $gudangId || ! $gudang = GudangBarang::find($gudangId)) {
                continue;
            }

            $detail = DetailBarangKeluar::where('pindah_gudang_id', $transferId)
                ->where('gudang_barang_id', $gudang->id)
                ->first();

            if ($detail) {
                $detail->update(['keterangan' => $row['keterangan'] ?? null]);
            } else {
                DetailBarangKeluar::create([
                    'pindah_gudang_id' => $transferId,
                    'barang_id' => $gudang->barang_id,
                    'gudang_barang_id' => $gudang->id,
                    'status' => 1,
                    'keterangan' => $row['keterangan'] ?? null,
                ]);
            }

            $gudang->update(['status' => 3]);
            $keptIds[] = $gudang->id;
        }

        $removed = DetailBarangKeluar::where('pindah_gudang_id', $transferId)
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('gudang_barang_id', $keptIds))
            ->get();

        foreach ($removed as $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->update(['status' => 1]);
            $detail->forceDelete();
        }
    }
}
