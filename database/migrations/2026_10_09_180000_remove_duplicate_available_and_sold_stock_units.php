<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The same physical stock unit can be represented twice when a deleted sale
     * returned its unit to stock and the serial was then entered again and sold
     * through a new sale. Each serial number then shows up as both available
     * stock and sold stock.
     *
     * This repairs every serial that, for the same barang, exists on at least one
     * available unit and on a unit sold through an active sale. The earliest
     * stock-in is kept as the original unit, any active sale is reconnected to it,
     * and the duplicate units plus their stock-in records are removed.
     */
    public function up(): void
    {
        $groups = DB::table('gudang_barangs')
            ->whereNotNull('serial_number_id')
            ->whereRaw('TRIM(serial_number_id) <> ?', [''])
            ->whereRaw('LOWER(TRIM(serial_number_id)) <> ?', ['null'])
            ->selectRaw('barang_id, LOWER(TRIM(serial_number_id)) as sn')
            ->groupBy('barang_id', 'sn')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $group) {
            $this->repairGroup((int) $group->barang_id, $group->sn);
        }
    }

    private function repairGroup(int $barangId, string $serial): void
    {
        $units = DB::table('gudang_barangs')
            ->where('barang_id', $barangId)
            ->whereRaw('LOWER(TRIM(serial_number_id)) = ?', [$serial])
            ->orderBy('id')
            ->get()
            ->map(function ($unit) {
                $unit->activeDetail = DB::table('detail_penjualans as dp')
                    ->join('penjualans as p', 'p.id', '=', 'dp.penjualan_id')
                    ->where('dp.gudang_barang_id', $unit->id)
                    ->whereNull('dp.deleted_at')
                    ->whereNull('p.deleted_at')
                    ->first(['dp.id', 'p.created_at']);

                $unit->enteredAt = DB::table('detail_barang_masuks as dbm')
                    ->join('stock_ins as si', 'si.id', '=', 'dbm.stock_in_id')
                    ->where('dbm.gudang_barang_id', $unit->id)
                    ->value('si.created_at') ?? $unit->created_at;

                return $unit;
            });

        $available = $units->filter(fn ($unit): bool => $unit->activeDetail === null && $unit->deleted_at === null);
        $sold = $units->filter(fn ($unit): bool => $unit->activeDetail !== null);

        // Nothing to repair when there is no phantom available unit or when more
        // than one copy is sold (a genuine double sale needs a human decision).
        if ($available->isEmpty() || $sold->isEmpty() || $sold->count() > 1) {
            return;
        }

        $keeper = $units
            ->sortBy(fn ($unit): string => $unit->enteredAt.'#'.str_pad((string) $unit->id, 10, '0', STR_PAD_LEFT))
            ->first();

        $removedIds = [];

        foreach ($units as $unit) {
            if ((int) $unit->id === (int) $keeper->id) {
                continue;
            }

            if ($unit->activeDetail !== null) {
                DB::table('detail_penjualans')
                    ->where('id', $unit->activeDetail->id)
                    ->update([
                        'gudang_barang_id' => $keeper->id,
                        'serial_number_id' => is_numeric($keeper->serial_number_id)
                            ? (int) $keeper->serial_number_id
                            : null,
                    ]);
            }

            $removedIds[] = $unit->id;
        }

        if ($keeper->activeDetail === null) {
            DB::table('gudang_barangs')
                ->where('id', $keeper->id)
                ->update([
                    'deleted_at' => $sold->first()->activeDetail->created_at,
                    'updated_at' => now(),
                ]);
        }

        $stockInIds = DB::table('detail_barang_masuks')
            ->whereIn('gudang_barang_id', $removedIds)
            ->pluck('stock_in_id')
            ->unique()
            ->all();

        DB::table('detail_barang_masuks')->whereIn('gudang_barang_id', $removedIds)->delete();
        DB::table('data_barang_keluars')->whereIn('gudang_barang_id', $removedIds)->delete();
        DB::table('gudang_barangs')->whereIn('id', $removedIds)->delete();

        foreach ($stockInIds as $stockInId) {
            $stillHasUnits = DB::table('detail_barang_masuks')
                ->where('stock_in_id', $stockInId)
                ->exists();

            if (! $stillHasUnits) {
                DB::table('stock_ins')->where('id', $stockInId)->delete();
            }
        }
    }

    /**
     * Data repair migration; the duplicate entries cannot be reconstructed.
     */
    public function down(): void
    {
        //
    }
};
