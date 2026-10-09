<?php

use App\Models\GudangBarang;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;

return new class extends Migration
{
    /**
     * Return stock units that belong to sales (or removed sale lines) but were
     * never restored when the sale was deleted. A unit is only restored when it
     * has at least one sale line, is not attached to any active sale, and is not
     * consumed by an active barang keluar.
     */
    public function up(): void
    {
        GudangBarang::withTrashed()
            ->whereNotNull('deleted_at')
            ->whereExists(fn (Builder $query): Builder => $query
                ->selectRaw('1')
                ->from('detail_penjualans')
                ->whereColumn('detail_penjualans.gudang_barang_id', 'gudang_barangs.id'))
            ->whereNotExists(fn (Builder $query): Builder => $query
                ->selectRaw('1')
                ->from('detail_penjualans')
                ->join('penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                ->whereColumn('detail_penjualans.gudang_barang_id', 'gudang_barangs.id')
                ->whereNull('detail_penjualans.deleted_at')
                ->whereNull('penjualans.deleted_at'))
            ->whereNotExists(fn (Builder $query): Builder => $query
                ->selectRaw('1')
                ->from('data_barang_keluars')
                ->whereColumn('data_barang_keluars.gudang_barang_id', 'gudang_barangs.id')
                ->whereNull('data_barang_keluars.deleted_at'))
            ->get()
            ->each(function (GudangBarang $unit): void {
                $unit->restore();
                $unit->forceFill(['status' => 1])->save();
            });
    }

    public function down(): void
    {
        // Data restore is not reversible: we cannot know which units were
        // deleted before this migration ran.
    }
};
