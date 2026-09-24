<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GudangBarang extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['barang_id', 'serial_number_id', 'toko_id', 'status'];

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

    public function barang_pindah()
    {
        return $this->belongsToMany(Barang::class, 'detail_barang_keluars', 'pindah_gudang_id', 'barang_id')->select('barangs.id','barangs.nama_product', 'barangs.satuan')->selectRaw('count(detail_barang_keluars.barang_id) as pivot_count')->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'detail_barang_keluars.pindah_gudang_id', 'detail_barang_keluars.barang_id');
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
