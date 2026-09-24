<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockIn extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['barang_id', 'po_id', 'jumlah', 'serial_number', 'tanggal_masuk', 'harga_beli', 'harga_jual', 'price_list', 'made_in', 'supplier', 'keterangan', 'type_serial_number', 'toko_id'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function detail_barang_masuk()
    {
        return $this->hasMany(DetailBarangMasuk::class);
    }

    public function gudang_barang()
    {
        return $this->belongsToMany(GudangBarang::class, 'detail_barang_masuks')->withTrashed();
    }

    public function po()
    {
        return $this->belongsTo(Po::class);
    }
}
