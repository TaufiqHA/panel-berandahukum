<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'kode_barang_keluar',
        'nama_penerima',
        'alamat_penerima',
        'telepon_penerima',
        'toko_id',
        'waktu',
        'nama_sales',
        'status',
        'keterangan',
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
        return $this->belongsToMany(GudangBarang::class, DataBarangKeluar::class)->withTrashed();
    }

    public function data_barang_keluar()
    {
        return $this->hasMany(DataBarangKeluar::class);
    }

    public function data_barang_keluar_group()
    {
        return $this->hasMany(DataBarangKeluar::class)->select('barang_id', DB::raw('count(*) as total'))->groupBy('barang_id');
    }

    public function serial_number()
    {
        return $this->belongsToMany(SerialNumber::class, 'detail_penjualans')->whereNull('detail_penjualans.deleted_at');
    }

    public function barang_pembelian()
    {
        return $this->belongsToMany(
            Barang::class, 'data_barang_keluars', 'barang_keluar_id', 'barang_id')
            ->select('barangs.id','barangs.nama_product', 'barangs.satuan')
            ->selectRaw('count(data_barang_keluars.barang_id) as pivot_count')
            ->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'data_barang_keluars.barang_keluar_id', 'data_barang_keluars.barang_id')->orderBy('data_barang_keluars.id');
    }

    public function barang_group()
    {
        return $this->belongsToMany(GudangBarang::class, DataBarangKeluar::class)->withTrashed()->groupBy('barang_id', 'gudang_barangs.id', 'gudang_barangs.serial_number', 'gudang_barangs.toko_id', 'gudang_barangs.status', 'gudang_barangs.created_at', 'gudang_barangs.updated_at', 'gudang_barangs.deleted_at', 'detail_penjualans.penjualan_id', 'detail_penjualans.gudang_barang_id');
    }
}
