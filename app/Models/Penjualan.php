<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Deleting a sale always returns the stock units it consumed.
     */
    protected static function booted(): void
    {
        static::deleting(function (Penjualan $penjualan): void {
            $penjualan->restoreSoldUnits();
        });
    }

    protected $fillable = [
        'date',
        'kode_penjualan',
        'nama_pembeli',
        'alamat_pembeli',
        'telepon',
        'metode_pembayaran',
        'toko_id',
        'payment_status',
        'discount_type',
        'discount_value',
        'waktu',
        'subtotal',
        'total_pembayaran',
        'dp_payment',
        'nama_sales',
        'ppn',
        'sisa',
        'status',
        'keterangan',
        'show_infopembayaran',
        'show_option',
        'option_text',
        'show_project',
        'nama_project',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsToMany(GudangBarang::class, DetailPenjualan::class)->withTrashed();
    }

    public function detail_penjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    /**
     * Return every stock unit consumed by this sale back to available stock.
     *
     * Active sale lines are always returned. Lines that were removed from the
     * sale earlier should already have returned their unit, but detached
     * legacy/imported lines may have left the unit soft deleted; those are
     * recovered too so the serial becomes selectable again.
     */
    public function restoreSoldUnits(): void
    {
        $restoredIds = [];

        foreach ($this->detail_penjualan()->get() as $detail) {
            $unit = $this->resolveDetailUnit($detail, $restoredIds);

            if ($unit === null) {
                continue;
            }

            $this->returnUnitToStock($unit);
            $restoredIds[] = $unit->id;
        }

        $this->detail_penjualan()->delete();

        foreach ($this->detail_penjualan()->onlyTrashed()->get() as $detail) {
            $unit = $this->resolveDetailUnit($detail, $restoredIds);

            if ($unit === null || $unit->deleted_at === null) {
                continue;
            }

            // Only units this sale consumed and that are not attached to
            // another active sale are safe to return to stock.
            if (! $unit->deleted_at->equalTo($this->created_at)) {
                continue;
            }

            if ($unit->detail_penjualan()->exists()) {
                continue;
            }

            $this->returnUnitToStock($unit);
            $restoredIds[] = $unit->id;
        }
    }

    /**
     * Returning a unit to stock must also clear the legacy "sold" status so it
     * shows up again as an available serial number.
     */
    private function returnUnitToStock(GudangBarang $unit): void
    {
        $unit->restore();
        $unit->forceFill(['status' => 1])->save();
    }

    /**
     * Resolve the stock unit consumed by a sale line.
     *
     * Imported sales sometimes stored the stock unit id in the serial number
     * column or replaced it with 0, leaving the unit detached from its sale.
     * The unit is recovered from the moment the sale was created, matching the
     * rule used by {@see GudangBarang::resolvePenjualan()}.
     *
     * @param  array<int, int>  $excluded  Units already claimed by earlier lines.
     */
    public function resolveDetailUnit(DetailPenjualan $detail, array $excluded = []): ?GudangBarang
    {
        if (! empty($detail->gudang_barang_id)) {
            return GudangBarang::withTrashed()->find($detail->gudang_barang_id);
        }

        // Legacy rows may store the stock unit id in the serial number column.
        if (! empty($detail->serial_number_id)) {
            $unit = GudangBarang::withTrashed()->find($detail->serial_number_id);

            if ($unit !== null && (int) $unit->barang_id === (int) $detail->barang_id) {
                return $unit;
            }
        }

        $query = GudangBarang::withTrashed()
            ->where('barang_id', $detail->barang_id)
            ->where('deleted_at', $this->created_at);

        if ($excluded !== []) {
            $query->whereNotIn('id', $excluded);
        }

        return $query->first();
    }

    public function detail_penjualan_group()
    {
        return $this->hasMany(DetailPenjualan::class)->select('barang_id', DB::raw('count(*) as total'))->groupBy('barang_id');
    }

    public function serial_number()
    {
        return $this->belongsToMany(SerialNumber::class, 'detail_penjualans')->whereNull('detail_penjualans.deleted_at');
    }

    public function barang_pembelian()
    {
        return $this->belongsToMany(
            Barang::class, 'detail_penjualans', 'penjualan_id', 'barang_id')
            ->whereNull('detail_penjualans.deleted_at')
            ->select('barangs.id', 'barangs.nama_product', 'barangs.satuan', 'detail_penjualans.price', 'detail_penjualans.discount')
            ->selectRaw('count(detail_penjualans.barang_id) as pivot_count')
            ->groupBy('barangs.kategori_id', 'barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan', 'barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'detail_penjualans.penjualan_id', 'detail_penjualans.barang_id', 'detail_penjualans.price', 'detail_penjualans.discount')->orderBy('detail_penjualans.id');
    }

    public function barang_group()
    {
        return $this->belongsToMany(GudangBarang::class, DetailPenjualan::class)->withTrashed()->groupBy('barang_id', 'gudang_barangs.id', 'gudang_barangs.serial_number', 'gudang_barangs.toko_id', 'gudang_barangs.status', 'gudang_barangs.created_at', 'gudang_barangs.updated_at', 'gudang_barangs.deleted_at', 'detail_penjualans.penjualan_id', 'detail_penjualans.gudang_barang_id');
    }
}
