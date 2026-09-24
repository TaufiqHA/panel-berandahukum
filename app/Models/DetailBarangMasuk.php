<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailBarangMasuk extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['stock_in_id', 'gudang_barang_id'];

    public function pindah_gudang()
    {
        return $this->belongsTo(PindahGudang::class);
    }

	public function stock_in()
    {
        return $this->belongsTo(StockIn::class);
    }    

    public function gudang_barang()
    {
        return $this->belongsTo(GudangBarang::class);
    }
}
