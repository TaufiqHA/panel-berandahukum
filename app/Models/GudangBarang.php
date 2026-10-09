<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GudangBarang extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['barang_id', 'serial_number_id', 'toko_id', 'status'];

    /**
     * Cached result of {@see resolvePenjualan()} so the buyer columns do not
     * trigger the fallback query more than once per unit.
     */
    private ?Penjualan $resolvedPenjualan = null;

    private bool $penjualanResolved = false;

    public function serial_number()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function detail_penjualan()
    {
        return $this->hasOne(DetailPenjualan::class);
    }

    /**
     * Resolve the sale a unit was sold through, including historical details.
     *
     * A sale detail that is still active means the unit is genuinely part of a
     * sale. A unit that is back in stock is never sold, even when a stale
     * (soft-deleted) sale detail remains from a sale it was removed from. Only
     * for soft-deleted units do we fall back to a soft-deleted detail so the
     * buyer information is still available.
     *
     * Imported sales sometimes stored `detail_penjualans.gudang_barang_id` as 0,
     * leaving the unit detached from its sale. The unit is soft deleted at the
     * exact moment the sale detail is created, so the orphaned detail is
     * recovered by matching that timestamp together with the item.
     */
    public function resolvePenjualan(): ?Penjualan
    {
        if ($this->penjualanResolved) {
            return $this->resolvedPenjualan;
        }

        $this->penjualanResolved = true;

        $penjualan = $this->detail_penjualan?->penjualan;

        if ($penjualan !== null) {
            return $this->resolvedPenjualan = $penjualan;
        }

        if ($this->deleted_at === null) {
            return $this->resolvedPenjualan = null;
        }

        $historical = DetailPenjualan::withTrashed()
            ->where('gudang_barang_id', $this->id)
            ->whereHas('penjualan')
            ->latest('id')
            ->first();

        if ($historical !== null) {
            return $this->resolvedPenjualan = $historical->penjualan;
        }

        return $this->resolvedPenjualan = Penjualan::query()
            ->where('created_at', $this->deleted_at)
            ->whereHas('detail_penjualan', fn (Builder $query): Builder => $query
                ->withTrashed()
                ->where('barang_id', $this->barang_id)
                ->where(fn (Builder $query): Builder => $query
                    ->whereNull('gudang_barang_id')
                    ->orWhere('gudang_barang_id', 0)))
            ->first();
    }

    public function barang_pindah()
    {
        return $this->belongsToMany(Barang::class, 'detail_barang_keluars', 'pindah_gudang_id', 'barang_id')->select('barangs.id', 'barangs.nama_product', 'barangs.satuan')->selectRaw('count(detail_barang_keluars.barang_id) as pivot_count')->groupBy('barangs.kategori_id', 'barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan', 'barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'detail_barang_keluars.pindah_gudang_id', 'detail_barang_keluars.barang_id');
    }

    public function detail_barang_masuk()
    {
        return $this->hasOne(DetailBarangMasuk::class);
    }

    public function detail_barang_keluar()
    {
        return $this->hasOne(DetailBarangKeluar::class);
    }
}
