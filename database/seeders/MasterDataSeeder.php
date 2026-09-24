<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Toko;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Setting;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Toko
        $toko1 = Toko::updateOrCreate(
            ['nama_toko' => 'Toko Pusat Utama'],
            [
                'alamat_toko' => 'Jl. Jendral Sudirman No. 1, Jakarta Pusat',
            ]
        );

        $toko2 = Toko::updateOrCreate(
            ['nama_toko' => 'Toko Cabang Selatan'],
            [
                'alamat_toko' => 'Jl. TB Simatupang No. 8, Jakarta Selatan',
            ]
        );

        // 2. Kategori
        $kategori1 = Kategori::updateOrCreate(['nama_kategori' => 'Elektronik']);
        $kategori2 = Kategori::updateOrCreate(['nama_kategori' => 'Furniture']);
        $kategori3 = Kategori::updateOrCreate(['nama_kategori' => 'ATK']);

        // 3. Supplier
        $supplier1 = Supplier::updateOrCreate(
            ['nama_supplier' => 'PT Mitra Sejati'],
            [
                'sales' => 'Budi Santoso',
                'alamat' => 'Kawasan Industri Pulogadung No. 45'
            ]
        );

        $supplier2 = Supplier::updateOrCreate(
            ['nama_supplier' => 'CV Makmur Jaya'],
            [
                'sales' => 'Siti Aisyah',
                'alamat' => 'Jl. Pegangsaan Timur No. 10'
            ]
        );

        // 4. Barang
        Barang::updateOrCreate(
            ['nama_product' => 'Laptop Asus ROG Zephyrus'],
            [
                'kategori_id' => $kategori1->id,
                'merk' => 'Asus',
                'satuan' => 'Unit',
                'warna' => 'Hitam',
                'berat' => '2kg',
                'wajib_serial_number' => 1,
            ]
        );
        
        Barang::updateOrCreate(
            ['nama_product' => 'Meja Kerja Ergonomis'],
            [
                'kategori_id' => $kategori2->id,
                'merk' => 'IKEA',
                'satuan' => 'Pcs',
                'warna' => 'Putih',
                'berat' => '15kg',
                'wajib_serial_number' => 0,
            ]
        );

        // 5. Setting
        Setting::updateOrCreate(
            ['toko_id' => $toko1->id],
            [
                'cara_pembayaran' => '<ul><li>Transfer Bank BCA: 123456789 (a.n. Perusahaan)</li><li>Mandiri: 987654321</li><li>Pembayaran Tunai di Kasir</li></ul>',
            ]
        );
    }
}
