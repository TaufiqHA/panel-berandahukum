<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'serial_number_id',
        'discount',
        'price',
    ];

    public function serial_number()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
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
