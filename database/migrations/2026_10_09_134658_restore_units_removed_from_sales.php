<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Items removed from a sale before the restore fix could not always be
     * returned to stock: legacy sale lines lost their `gudang_barang_id` link
     * (stored as 0), so removing the line left the stock unit soft deleted.
     *
     * A unit is sold only while an active sale line points at it. This migration
     * returns the units that are still soft deleted even though the sale lines
     * that consumed them are gone (soft deleted) or no longer linked.
     */
    public function up(): void
    {
        $unitIds = DB::table('gudang_barangs')
            ->whereNotNull('deleted_at')
            ->where(function ($query): void {
                // A soft-deleted line previously consumed this unit.
                $query->whereExists(function ($sub): void {
                    $sub->selectRaw('1')
                        ->from('detail_penjualans')
                        ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                        ->whereNull('penjualans.deleted_at')
                        ->whereColumn('detail_penjualans.gudang_barang_id', 'gudang_barangs.id')
                        ->whereNotNull('detail_penjualans.deleted_at');
                })->orWhereExists(function ($sub): void {
                    // Legacy orphaned line: the unit link was lost (0/null).
                    $sub->selectRaw('1')
                        ->from('detail_penjualans')
                        ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                        ->whereNull('penjualans.deleted_at')
                        ->whereColumn('penjualans.created_at', 'gudang_barangs.deleted_at')
                        ->whereColumn('detail_penjualans.barang_id', 'gudang_barangs.barang_id')
                        ->where(fn ($sub) => $sub->whereNull('detail_penjualans.gudang_barang_id')
                            ->orWhere('detail_penjualans.gudang_barang_id', 0));
                });
            })
            // Keep units that are still sold through an active linked line.
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('detail_penjualans')
                    ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                    ->whereNull('penjualans.deleted_at')
                    ->whereColumn('detail_penjualans.gudang_barang_id', 'gudang_barangs.id')
                    ->whereNull('detail_penjualans.deleted_at');
            })
            // Keep units that are still sold through an active orphaned line.
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('detail_penjualans')
                    ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                    ->whereNull('penjualans.deleted_at')
                    ->whereNull('detail_penjualans.deleted_at')
                    ->whereColumn('penjualans.created_at', 'gudang_barangs.deleted_at')
                    ->whereColumn('detail_penjualans.barang_id', 'gudang_barangs.barang_id')
                    ->where(fn ($sub) => $sub->whereNull('detail_penjualans.gudang_barang_id')
                        ->orWhere('detail_penjualans.gudang_barang_id', 0));
            })
            ->pluck('id');

        if ($unitIds->isEmpty()) {
            return;
        }

        DB::table('gudang_barangs')
            ->whereIn('id', $unitIds)
            ->update([
                'deleted_at' => null,
                'status' => 1,
                'updated_at' => now(),
            ]);
    }

    /**
     * Data repair migration; there is no safe automatic rollback.
     */
    public function down(): void
    {
        //
    }
};
