<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PindahGudang extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['date', 'no_ref', 'from', 'to', 'status'];

    public function detail_barang_keluar()
    {
        return $this->hasMany(DetailBarangKeluar::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'from');
    }

    public function toko_to()
    {
        return $this->belongsTo(Toko::class, 'to');
    }

    public function data_detail_barang()
    {
        return $this->belongsToMany(Barang::class, 'detail_barang_keluars', 'pindah_gudang_id', 'barang_id')->whereNull('detail_barang_keluars.deleted_at');
    }

    public function data_detail_serial_number()
    {
        return $this->belongsToMany(GudangBarang::class, 'detail_barang_keluars', 'pindah_gudang_id', 'gudang_barang_id')->whereNull('detail_barang_keluars.deleted_at');
    }

    public function barang_pindah()
    {
        return $this->belongsToMany(Barang::class, 'detail_barang_keluars', 'pindah_gudang_id', 'barang_id')->select('barangs.id','barangs.nama_product', 'barangs.satuan')->selectRaw('count(detail_barang_keluars.barang_id) as pivot_count')->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'detail_barang_keluars.pindah_gudang_id', 'detail_barang_keluars.barang_id');
    }
}
