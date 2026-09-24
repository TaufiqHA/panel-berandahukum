<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['kategori_id', 'nama_product','merk','satuan', 'warna', 'berat', 'ukuran', 'harga','keterangan', 'wajib_serial_number'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function stock_in()
    {
        return $this->hasMany(StockIn::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function detail_penjualan()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class);
    }

    public function gudang_barang()
    {
        return $this->hasMany(GudangBarang::class);
    }
}
