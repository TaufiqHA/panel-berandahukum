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
        return $this->hasOne(DetailPenjualan::class)->withTrashed();
    }

    /**
     * Resolve the sale a unit was sold through, including trashed sale details.
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

        if ($penjualan !== null || $this->deleted_at === null) {
            return $this->resolvedPenjualan = $penjualan;
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
