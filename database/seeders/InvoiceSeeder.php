<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Toko;
use App\Models\Barang;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $toko = Toko::first();
        $barang = Barang::first();

        // Check if master data is seeded
        if (!$toko || !$barang) {
            $this->command->warn('Toko or Barang data is missing. Please run MasterDataSeeder first.');
            return;
        }

        $invoice = Invoice::updateOrCreate(
            ['kode_invoice' => 'INV-' . date('Ymd') . '-001'],
            [
                'date' => Carbon::today(),
                'nama_pembeli' => 'PT Perusahaan Contoh',
                'alamat_pembeli' => 'Jalan Contoh No. 123, Jakarta Raya',
                'tlp_pembeli' => '081234567890',
                'metode_pembayaran' => 'Transfer Bank',
                'toko_id' => $toko->id,
                'payment_status' => 'Lunas',
                'discount_type' => 'Tidak Ada',
                'discount_value' => '0',
                'waktu' => '0',
                'subtotal' => 25000000,
                'total_pembayaran' => 25000000,
                'dp_payment' => 0,
                'nama_sales' => 'Admin', // Sesuaikan nama sales/user di seeder
                'ppn' => 0,
                'sisa' => 0,
                'status' => '1',
                'keterangan' => 'Pembelian unit laptop langsung dari toko cabang pertama.',
                'show_infopembayaran' => '1',
                'show_option' => '0',
                'option_text' => null,
                'show_project' => '0',
                'nama_project' => null,
            ]
        );

        InvoiceDetail::updateOrCreate(
            ['invoice_id' => $invoice->id, 'barang_id' => $barang->id],
            [
                'jumlah' => 1,
                'discount' => 0,
                'price' => 25000000,
                'subtotal' => 25000000,
            ]
        );
        
        $this->command->info('Invoice data seeded successfully.');
    }
}
