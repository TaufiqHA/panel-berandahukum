<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'barang_id',
        'jumlah',
        'discount',
        'price',
        'subtotal'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
    
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
