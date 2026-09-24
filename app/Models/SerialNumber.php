<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialNumber extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['stock_in_id', 'serial_number'];

    public function stock_in()
    {
        return $this->belongsTo(StockIn::class);
    }
}
