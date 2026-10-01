<?php

namespace App\Filament\Support;

class TransactionData
{
    /**
     * Apply the legacy fallback values for buyer/sales names so records with
     * an empty buyer name can still be stored as a draft.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withBuyerDefaults(array $data): array
    {
        $data['nama_pembeli'] = filled($data['nama_pembeli'] ?? null) ? $data['nama_pembeli'] : '-draft-';
        $data['nama_sales'] = filled($data['nama_sales'] ?? null) ? $data['nama_sales'] : '-';

        return $data;
    }
}
