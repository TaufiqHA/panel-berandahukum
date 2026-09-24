<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'kode_quotation',
        'nama_pembeli',
        'alamat_pembeli',
        'telepon',
        'toko_id',
        'nama_sales',
        'subtotal',
        'ppn',
        'status',
        'keterangan',
        'show_infopembayaran',
        'show_option',
        'option_text',
        'show_project',
        'nama_project',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'quotation_details')->whereNull('quotation_details.deleted_at');
    }

    public function detail_quotation()
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function barang_pembelian()
    {
        return $this->belongsToMany(Barang::class, 'quotation_details', 'quotation_id', 'barang_id')->select('barangs.nama_product', 'quotation_details.price', 'quotation_details.discount')->selectRaw('count(quotation_details.barang_id) as pivot_count')->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'quotation_details.quotation_id', 'quotation_details.barang_id', 'quotation_details.price', 'quotation_details.discount')->whereNull('quotation_details.deleted_at')->orderBy('quotation_details.id');
    }
}
