<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataBarangKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'gudang_barang_id',
        'discount',
        'price',
    ];

    public function barang_keluar()
    {
        return $this->belongsTo(BarangKeluar::class);
    }
    
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang_barang()
    {
        return $this->belongsTo(GudangBarang::class)->withTrashed();
    }
}
