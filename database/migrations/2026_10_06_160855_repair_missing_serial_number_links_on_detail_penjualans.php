<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Some imported sales lost the link to their stock unit, so `gudang_barang_id`
     * was stored as 0 and the serial number no longer showed on the invoice.
     *
     * The sold units still exist (soft deleted) and were deleted at the exact same
     * second the sale was created. We reconnect each orphaned detail row to its unit
     * when the number of available units matches the number of orphaned rows.
     */
    public function up(): void
    {
        $ambiguousCreatedAt = DB::table('penjualans')
            ->select('created_at')
            ->whereNotNull('created_at')
            ->groupBy('created_at')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('created_at')
            ->all();

        $penjualanIds = DB::table('detail_penjualans')
            ->where(function ($query): void {
                $query->whereNull('gudang_barang_id')->orWhere('gudang_barang_id', 0);
            })
            ->distinct()
            ->pluck('penjualan_id');

        foreach ($penjualanIds as $penjualanId) {
            $penjualan = DB::table('penjualans')->where('id', $penjualanId)->first();

            if ($penjualan === null || $penjualan->created_at === null) {
                continue;
            }

            if (in_array($penjualan->created_at, $ambiguousCreatedAt, true)) {
                continue;
            }

            $detailsByBarang = DB::table('detail_penjualans')
                ->where('penjualan_id', $penjualanId)
                ->where(function ($query): void {
                    $query->whereNull('gudang_barang_id')->orWhere('gudang_barang_id', 0);
                })
                ->orderBy('id')
                ->get()
                ->groupBy('barang_id');

            foreach ($detailsByBarang as $barangId => $details) {
                $units = DB::table('gudang_barangs')
                    ->where('barang_id', $barangId)
                    ->where('deleted_at', $penjualan->created_at)
                    ->whereNotIn('id', function ($query): void {
                        $query->select('gudang_barang_id')
                            ->from('detail_penjualans')
                            ->whereNotNull('gudang_barang_id')
                            ->where('gudang_barang_id', '!=', 0);
                    })
                    ->orderBy('id')
                    ->get();

                if ($units->isEmpty() || $units->count() !== $details->count()) {
                    continue;
                }

                foreach ($details->values() as $index => $detail) {
                    $unit = $units->get($index);

                    DB::table('detail_penjualans')
                        ->where('id', $detail->id)
                        ->update([
                            'gudang_barang_id' => $unit->id,
                            'serial_number_id' => is_numeric($unit->serial_number_id) ? (int) $unit->serial_number_id : null,
                        ]);
                }
            }
        }
    }

    /**
     * Data repair migration; there is no safe automatic rollback.
     */
    public function down(): void
    {
        //
    }
};
