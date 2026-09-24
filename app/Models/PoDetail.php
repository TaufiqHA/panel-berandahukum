<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_id',
        'barang_id',
        'jumlah',
        'discount',
        'price',
        'subtotal'
    ];

    public function po()
    {
        return $this->belongsTo(Po::class);
    }
    
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
