<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The DM6SE units sold to "RM Pagi Sore Gatsu" exist twice: after the earlier
     * "Pagi Sore Gatsu" sale was deleted the original units returned to stock, but
     * the same serial numbers were entered again as a new stock-in and sold through
     * the active sale. Every serial number therefore showed up as both available
     * stock and sold stock.
     *
     * The active sale is kept and reconnected to the original units; the duplicate
     * stock-in and the re-entered units are removed.
     */
    public function up(): void
    {
        $penjualan = DB::table('penjualans')
            ->where('kode_penjualan', 'PJ - 0001545')
            ->whereNull('deleted_at')
            ->first();

        if ($penjualan === null) {
            return;
        }

        $details = DB::table('detail_penjualans')
            ->where('penjualan_id', $penjualan->id)
            ->whereNull('deleted_at')
            ->get();

        $duplicateUnitIds = [];

        foreach ($details as $detail) {
            $duplicateUnit = DB::table('gudang_barangs')
                ->where('id', $detail->gudang_barang_id)
                ->first();

            if ($duplicateUnit === null || empty($duplicateUnit->serial_number_id)) {
                continue;
            }

            // The original physical unit still sits in stock under the same serial.
            $originalUnit = DB::table('gudang_barangs')
                ->where('barang_id', $duplicateUnit->barang_id)
                ->where('serial_number_id', $duplicateUnit->serial_number_id)
                ->where('id', '!=', $duplicateUnit->id)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->first();

            if ($originalUnit === null) {
                continue;
            }

            DB::table('detail_penjualans')
                ->where('id', $detail->id)
                ->update([
                    'gudang_barang_id' => $originalUnit->id,
                    'serial_number_id' => is_numeric($originalUnit->serial_number_id)
                        ? (int) $originalUnit->serial_number_id
                        : null,
                ]);

            DB::table('gudang_barangs')
                ->where('id', $originalUnit->id)
                ->update([
                    'deleted_at' => $penjualan->created_at,
                    'updated_at' => now(),
                ]);

            $duplicateUnitIds[] = $duplicateUnit->id;
        }

        if ($duplicateUnitIds === []) {
            return;
        }

        $stockInIds = DB::table('detail_barang_masuks')
            ->whereIn('gudang_barang_id', $duplicateUnitIds)
            ->pluck('stock_in_id')
            ->unique()
            ->all();

        DB::table('detail_barang_masuks')->whereIn('gudang_barang_id', $duplicateUnitIds)->delete();
        DB::table('data_barang_keluars')->whereIn('gudang_barang_id', $duplicateUnitIds)->delete();
        DB::table('gudang_barangs')->whereIn('id', $duplicateUnitIds)->delete();

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
