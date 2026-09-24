<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Po extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'kode_po',
        'supplier_id',
        'nama_purchase',
        'nama_supplier',
        'alamat_supplier',
        'telepon',
        'toko_id',
        'nama_sales',
        'subtotal',
        'po_dp',
        'ppn',
        'status',
        'status_terima',
		'status_bayar',
        'keterangan',
        'show_tempo',
        'jatuh_tempo',
        'alamat_kirim',
        'jenis_brang',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'po_details')->whereNull('po_details.deleted_at');
    }

    public function detail_po()
    {
        return $this->hasMany(PoDetail::class);
    }

    public function barang_pembelian()
    {
        return $this->belongsToMany(Barang::class, 'po_details', 'po_id', 'barang_id')->select('barangs.nama_product', 'po_details.price', 'po_details.discount')->selectRaw('count(po_details.barang_id) as pivot_count')->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'po_details.po_id', 'po_details.barang_id', 'po_details.price', 'po_details.discount')->whereNull('po_details.deleted_at')->orderBy('po_details.id');
    }

    public function stock_in()
    {
        return $this->hasMany(Po::class);
    }
}
