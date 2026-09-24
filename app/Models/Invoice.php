<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'kode_invoice',
        'nama_pembeli',
        'alamat_pembeli',
        'tlp_pembeli',
        'metode_pembayaran',
        'toko_id',
        'payment_status',
        'discount_type',
        'discount_value',
        'waktu',
        'subtotal',
        'total_pembayaran',
        'dp_payment',
        'nama_sales',
        'ppn',
        'sisa',
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
        return $this->belongsToMany(GudangBarang::class, InvoiceDetail::class)->withTrashed();
    }

    public function detail_invoice()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function detail_invoice_group()
    {
        return $this->hasMany(InvoiceDetail::class)->select('barang_id', DB::raw('count(*) as total'))->groupBy('barang_id');
    }

    public function serial_number()
    {
        return $this->belongsToMany(SerialNumber::class, 'detail_invoices')->whereNull('detail_invoices.deleted_at');
    }

    public function barang_pembelian()
    {
        return $this->belongsToMany(
            Barang::class, 'detail_invoices', 'invoice_id', 'barang_id')
            ->select('barangs.id','barangs.nama_product', 'barangs.satuan', 'detail_invoices.price', 'detail_invoices.discount')
            ->selectRaw('count(detail_invoices.barang_id) as pivot_count')
            ->groupBy('barangs.kategori_id','barangs.id', 'barangs.nama_product', 'barangs.merk', 'barangs.satuan','barangs.warna', 'barangs.berat', 'barangs.ukuran', 'barangs.keterangan', 'barangs.wajib_serial_number', 'detail_invoices.invoice_id', 'detail_invoices.barang_id', 'detail_invoices.price', 'detail_invoices.discount')->orderBy('detail_invoices.id');
    }

    // public function barang_group()
    // {
    //     return $this->belongsToMany(GudangBarang::class, InvoiceDetail::class)->withTrashed()->groupBy('barang_id', 'gudang_barangs.id', 'gudang_barangs.serial_number', 'gudang_barangs.toko_id', 'gudang_barangs.status', 'gudang_barangs.created_at', 'gudang_barangs.updated_at', 'gudang_barangs.deleted_at', 'detail_invoices.invoice_id', 'detail_invoices.gudang_barang_id');
    // }
}
