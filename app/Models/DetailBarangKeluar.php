<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailBarangKeluar extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['pindah_gudang_id', 'barang_id', 'gudang_barang_id', 'serial_number_id', 'status', 'keterangan'];

    public function serial_number()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
    }

    public function pindah_gudang()
    {
        return $this->belongsTo(PindahGudang::class);
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
