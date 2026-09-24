<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['stock_in_id', 'serial_number', 'toko_id'];

    public function stockInProductDetail()
    {
        return $this->hasOneThrough(StockIn::class, ProductDetail::class);
    }
}
