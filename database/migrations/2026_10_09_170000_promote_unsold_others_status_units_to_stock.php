<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Legacy data uses `gudang_barangs.status = 2`, shown as "Lainnya", for stock
     * units that were never marked as available. Those units are invisible to the
     * stock page because available stock is counted only for `status = 1`.
     *
     * A unit is only genuinely sold while an active sale line points at it, so the
     * status 2 units without an active sale (and not consumed by an active barang
     * keluar) are promoted to available stock.
     */
    public function up(): void
    {
        $unitIds = DB::table('gudang_barangs')
            ->where('status', 2)
            ->whereNull('deleted_at')
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('detail_penjualans')
                    ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                    ->whereColumn('detail_penjualans.gudang_barang_id', 'gudang_barangs.id')
                    ->whereNull('detail_penjualans.deleted_at')
                    ->whereNull('penjualans.deleted_at');
            })
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('data_barang_keluars')
                    ->whereColumn('data_barang_keluars.gudang_barang_id', 'gudang_barangs.id')
                    ->whereNull('data_barang_keluars.deleted_at');
            })
            ->pluck('id');

        if ($unitIds->isEmpty()) {
            return;
        }

        DB::table('gudang_barangs')
            ->whereIn('id', $unitIds)
            ->update([
                'status' => 1,
                'updated_at' => now(),
            ]);
    }

    /**
     * Data repair migration; the original status cannot be reconstructed safely.
     */
    public function down(): void
    {
        //
    }
};
