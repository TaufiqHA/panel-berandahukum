<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $fillable = ['nama_toko', 'alamat_toko', 'image'];

    public function stock_in()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'gudang_barangs');
    }

    public function toko_from()
    {
        return $this->hasOne(PindahGudang::class, 'from');
    }

    public function toko_to()
    {
        return $this->hasOne(PindahGudang::class, 'to');
    }
    
    public function gudang_barang()
    {
        return $this->hasMany(GudangBarang::class);
    }
}
