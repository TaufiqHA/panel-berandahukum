<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Toko;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Setting;
use App\Models\Signature;
use App\Models\StockIn;
use App\Models\SerialNumber;
use App\Models\GudangBarang;
use App\Models\DetailBarangMasuk;
use App\Models\PindahGudang;
use App\Models\DetailBarangKeluar;
use App\Models\Po;
use App\Models\PoDetail;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;

class ComprehensiveDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Memulai seeding data komprehensif untuk seluruh halaman...');

        // ==========================================
        // 1. TOKO (CABANG)
        // ==========================================
        $toko1 = Toko::updateOrCreate(
            ['nama_toko' => 'Toko Pusat Utama'],
            [
                'alamat_toko' => 'Jl. Jendral Sudirman No. 1, Jakarta Pusat',
                'image' => null,
            ]
        );

        $toko2 = Toko::updateOrCreate(
            ['nama_toko' => 'Toko Cabang Selatan'],
            [
                'alamat_toko' => 'Jl. TB Simatupang No. 8, Jakarta Selatan',
                'image' => null,
            ]
        );

        $toko3 = Toko::updateOrCreate(
            ['nama_toko' => 'Toko Cabang Barat'],
            [
                'alamat_toko' => 'Jl. Puri Indah Raya No. 15, Jakarta Barat',
                'image' => null,
            ]
        );

        $this->command->info('✓ Master Toko berhasil dibuat.');

        // ==========================================
        // 2. USERS & ROLES
        // ==========================================
        // Admin Pusat / Superuser (status = 1, status_admin = 2 agar bisa akses Report & User Management)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'status' => 1,
                'status_admin' => 2, // 2 = Superuser yang memiliki akses penuh ke menu users & report
                'toko_id' => $toko1->id,
                'user_menu' => 'Barang,Kategori Barang,Toko,Supplier,Setting,Barang Masuk,Barang Keluar,Stock,Pindah Toko,Search,Purchase Order,Penjualan,Invoice,Quotation,Report,User',
            ]
        );

        // Sales Staff
        User::updateOrCreate(
            ['email' => 'sales@admin.com'],
            [
                'name' => 'Agus (Sales)',
                'password' => Hash::make('password'),
                'status' => 2,
                'status_admin' => null,
                'toko_id' => $toko1->id,
                'user_menu' => 'Penjualan,Invoice,Quotation,Search',
            ]
        );

        // Gudang Staff
        User::updateOrCreate(
            ['email' => 'gudang@admin.com'],
            [
                'name' => 'Budi (Gudang)',
                'password' => Hash::make('password'),
                'status' => 2,
                'status_admin' => null,
                'toko_id' => $toko1->id,
                'user_menu' => 'Barang,Kategori Barang,Barang Masuk,Barang Keluar,Stock,Pindah Toko,Search',
            ]
        );

        // Manager Cabang
        User::updateOrCreate(
            ['email' => 'manager@admin.com'],
            [
                'name' => 'Citra (Manager)',
                'password' => Hash::make('password'),
                'status' => 2,
                'status_admin' => null,
                'toko_id' => $toko1->id,
                'user_menu' => 'Barang,User,Toko,Supplier,Barang Masuk,Stock,Setting,Search,Report,Quotation,Purchase Order,Pindah Toko,Penjualan,Kategori Barang,Invoice',
            ]
        );

        $this->command->info('✓ Master Users berhasil dibuat.');

        // ==========================================
        // 3. KATEGORI BARANG
        // ==========================================
        $katLaptop = Kategori::updateOrCreate(['nama_kategori' => 'Laptop & Komputer']);
        $katNetwork = Kategori::updateOrCreate(['nama_kategori' => 'Jaringan & Server']);
        $katAksesoris = Kategori::updateOrCreate(['nama_kategori' => 'Aksesoris Komputer']);
        $katFurniture = Kategori::updateOrCreate(['nama_kategori' => 'Office & Furniture']);

        $this->command->info('✓ Master Kategori berhasil dibuat.');

        // ==========================================
        // 4. SUPPLIER
        // ==========================================
        $supplier1 = Supplier::updateOrCreate(
            ['nama_supplier' => 'PT Teknologi Nusantara'],
            [
                'sales' => 'Hendra Setiawan',
                'alamat' => 'Kawasan Industri Jababeka Blok C-12, Cikarang',
            ]
        );

        $supplier2 = Supplier::updateOrCreate(
            ['nama_supplier' => 'CV Mitra Solusindo'],
            [
                'sales' => 'Dewi Lestari',
                'alamat' => 'Jl. Mangga Dua Raya No. 45, Jakarta Pusat',
            ]
        );

        $supplier3 = Supplier::updateOrCreate(
            ['nama_supplier' => 'PT Global Distribusi Komputindo'],
            [
                'sales' => 'Rian Pratama',
                'alamat' => 'Jl. Arteri Kelapa Dua No. 88, Kebon Jeruk, Jakarta Barat',
            ]
        );

        $this->command->info('✓ Master Supplier berhasil dibuat.');

        // ==========================================
        // 5. MASTER BARANG
        // ==========================================
        $barang1 = Barang::updateOrCreate(
            ['nama_product' => 'Laptop Asus ROG Zephyrus G14'],
            [
                'kategori_id' => $katLaptop->id,
                'merk' => 'Asus',
                'satuan' => 'Unit',
                'warna' => 'Eclipse Gray',
                'berat' => '1.7kg',
                'ukuran' => '14 Inch',
                'harga' => 28500000,
                'wajib_serial_number' => 1,
                'keterangan' => 'AMD Ryzen 9, 32GB RAM, 1TB SSD, RTX 4060',
            ]
        );

        $barang2 = Barang::updateOrCreate(
            ['nama_product' => 'Laptop Lenovo ThinkPad X1 Carbon'],
            [
                'kategori_id' => $katLaptop->id,
                'merk' => 'Lenovo',
                'satuan' => 'Unit',
                'warna' => 'Black Matte',
                'berat' => '1.1kg',
                'ukuran' => '14 Inch',
                'harga' => 24000000,
                'wajib_serial_number' => 1,
                'keterangan' => 'Intel Core i7 Gen 13, 16GB RAM, 512GB SSD',
            ]
        );

        $barang3 = Barang::updateOrCreate(
            ['nama_product' => 'Router Mikrotik CCR1009-7G-1C-1S+'],
            [
                'kategori_id' => $katNetwork->id,
                'merk' => 'Mikrotik',
                'satuan' => 'Unit',
                'warna' => 'Putih',
                'berat' => '2kg',
                'ukuran' => '1U Rackmount',
                'harga' => 6500000,
                'wajib_serial_number' => 1,
                'keterangan' => 'Cloud Core Router 9 Cores, 7 Gigabit Ethernet',
            ]
        );

        $barang4 = Barang::updateOrCreate(
            ['nama_product' => 'Mouse Wireless Logitech MX Master 3S'],
            [
                'kategori_id' => $katAksesoris->id,
                'merk' => 'Logitech',
                'satuan' => 'Pcs',
                'warna' => 'Graphite',
                'berat' => '140gr',
                'ukuran' => 'Standar',
                'harga' => 1550000,
                'wajib_serial_number' => 0,
                'keterangan' => 'Ergonomic performance mouse Bluetooth & Bolt receiver',
            ]
        );

        $barang5 = Barang::updateOrCreate(
            ['nama_product' => 'Meja Kerja Ergonomis Adjustable'],
            [
                'kategori_id' => $katFurniture->id,
                'merk' => 'ErgoDesk',
                'satuan' => 'Unit',
                'warna' => 'Oak Wood / Black Frame',
                'berat' => '25kg',
                'ukuran' => '140x70 cm',
                'harga' => 3800000,
                'wajib_serial_number' => 0,
                'keterangan' => 'Dual motor electric standing desk dengan memory preset',
            ]
        );

        $this->command->info('✓ Master Barang berhasil dibuat.');

        // ==========================================
        // 6. ACCOUNTING / SETTING
        // ==========================================
        Setting::updateOrCreate(
            ['toko_id' => $toko1->id],
            [
                'cara_pembayaran' => '<ul><li>Bank Central Asia (BCA): 123-456-7890 a.n. MelindaStore Pusat</li><li>Bank Mandiri: 987-654-3210 a.n. MelindaStore Pusat</li><li>Pembayaran Tunai di Kasir / Kas Kantor</li></ul>',
            ]
        );

        Setting::updateOrCreate(
            ['toko_id' => $toko2->id],
            [
                'cara_pembayaran' => '<ul><li>Bank Central Asia (BCA): 555-888-9999 a.n. MelindaStore Cabang Selatan</li><li>QRIS / EDC Merchant</li></ul>',
            ]
        );

        $this->command->info('✓ Master Setting / Accounting berhasil dibuat.');

        // ==========================================
        // 7. DIGITAL SIGNATURES
        // ==========================================
        // Sampel PNG transparan 1x1 data URI standar untuk testing tanda tangan
        $dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAAA8CAYAAACE8/7SAAAABHNCSVQICAgIfAhkiAAAAGhJREFUeJztwTEBAAAAwqD1T20JT6AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAuBqG5gABW3cE/AAAAABJRU5ErkJggg==';

        Signature::updateOrCreate(
            ['name' => 'Admin Utama'],
            ['signature' => $dummySig]
        );
        Signature::updateOrCreate(
            ['name' => 'Agus (Sales)'],
            ['signature' => $dummySig]
        );
        Signature::updateOrCreate(
            ['name' => 'Citra (Manager)'],
            ['signature' => $dummySig]
        );

        $this->command->info('✓ Master Digital Signatures berhasil dibuat.');

        // ==========================================
        // 8. BARANG MASUK (STOCK IN), GUDANG, & SERIAL NUMBERS
        // ==========================================
        // Batch 1: Laptop Asus ROG (3 unit) di Toko Pusat
        $stockIn1 = StockIn::updateOrCreate(
            [
                'toko_id' => $toko1->id,
                'barang_id' => $barang1->id,
                'tanggal_masuk' => Carbon::now()->subDays(20)->toDateString(),
            ],
            [
                'jumlah' => 3,
                'harga_beli' => 24000000,
                'harga_jual' => 28500000,
                'price_list' => 30000000,
                'made_in' => 'Taiwan',
                'supplier' => $supplier1->nama_supplier,
                'type_serial_number' => 1,
                'keterangan' => 'Pengadaan Laptop Gaming Batch Q3',
            ]
        );

        $snListLaptop = ['SN-ROG-2026-001', 'SN-ROG-2026-002', 'SN-ROG-2026-003'];
        foreach ($snListLaptop as $idx => $snStr) {
            $snRecord = SerialNumber::updateOrCreate(
                ['serial_number' => $snStr],
                ['stock_in_id' => $stockIn1->id]
            );

            $gudang1 = GudangBarang::updateOrCreate(
                [
                    'barang_id' => $barang1->id,
                    'serial_number_id' => $snRecord->id,
                    'toko_id' => $toko1->id,
                ],
                ['status' => 1] // 1: Ready in store
            );

            DetailBarangMasuk::updateOrCreate(
                ['stock_in_id' => $stockIn1->id, 'gudang_barang_id' => $gudang1->id]
            );
        }

        // Batch 2: ThinkPad X1 (2 unit) di Toko Pusat
        $stockIn2 = StockIn::updateOrCreate(
            [
                'toko_id' => $toko1->id,
                'barang_id' => $barang2->id,
                'tanggal_masuk' => Carbon::now()->subDays(15)->toDateString(),
            ],
            [
                'jumlah' => 2,
                'harga_beli' => 20000000,
                'harga_jual' => 24000000,
                'price_list' => 25000000,
                'made_in' => 'China',
                'supplier' => $supplier2->nama_supplier,
                'type_serial_number' => 1,
                'keterangan' => 'Pengadaan Laptop Bisnis ThinkPad',
            ]
        );

        $snListThinkPad = ['SN-TP-2026-001', 'SN-TP-2026-002'];
        foreach ($snListThinkPad as $idx => $snStr) {
            $snRecord = SerialNumber::updateOrCreate(
                ['serial_number' => $snStr],
                ['stock_in_id' => $stockIn2->id]
            );

            $gudang2 = GudangBarang::updateOrCreate(
                [
                    'barang_id' => $barang2->id,
                    'serial_number_id' => $snRecord->id,
                    'toko_id' => $toko1->id,
                ],
                ['status' => 1]
            );

            DetailBarangMasuk::updateOrCreate(
                ['stock_in_id' => $stockIn2->id, 'gudang_barang_id' => $gudang2->id]
            );
        }

        // Batch 3: Mouse Logitech (10 pcs) di Toko Pusat (tanpa serial number)
        $stockIn3 = StockIn::updateOrCreate(
            [
                'toko_id' => $toko1->id,
                'barang_id' => $barang4->id,
                'tanggal_masuk' => Carbon::now()->subDays(10)->toDateString(),
            ],
            [
                'jumlah' => 10,
                'harga_beli' => 1200000,
                'harga_jual' => 1550000,
                'price_list' => 1650000,
                'made_in' => 'Vietnam',
                'supplier' => $supplier3->nama_supplier,
                'type_serial_number' => 0,
                'keterangan' => 'Restock aksesoris mouse MX Master',
            ]
        );

        for ($i = 1; $i <= 10; $i++) {
            $gudangMouse = GudangBarang::firstOrCreate(
                [
                    'barang_id' => $barang4->id,
                    'toko_id' => $toko1->id,
                    'serial_number_id' => null,
                    'id' => 100 + $i, // manual specific ID agar tidak collision
                ],
                ['status' => 1]
            );

            DetailBarangMasuk::firstOrCreate(
                ['stock_in_id' => $stockIn3->id, 'gudang_barang_id' => $gudangMouse->id]
            );
        }

        // Batch 4: Router Mikrotik (2 unit) di Toko Cabang Selatan
        $stockIn4 = StockIn::updateOrCreate(
            [
                'toko_id' => $toko2->id,
                'barang_id' => $barang3->id,
                'tanggal_masuk' => Carbon::now()->subDays(5)->toDateString(),
            ],
            [
                'jumlah' => 2,
                'harga_beli' => 5000000,
                'harga_jual' => 6500000,
                'price_list' => 7000000,
                'made_in' => 'Latvia',
                'supplier' => $supplier1->nama_supplier,
                'type_serial_number' => 1,
                'keterangan' => 'Pengadaan Core Router untuk Cabang',
            ]
        );

        $snListMikrotik = ['SN-MT-2026-001', 'SN-MT-2026-002'];
        foreach ($snListMikrotik as $idx => $snStr) {
            $snRecord = SerialNumber::updateOrCreate(
                ['serial_number' => $snStr],
                ['stock_in_id' => $stockIn4->id]
            );

            $gudangRouter = GudangBarang::updateOrCreate(
                [
                    'barang_id' => $barang3->id,
                    'serial_number_id' => $snRecord->id,
                    'toko_id' => $toko2->id,
                ],
                ['status' => 1]
            );

            DetailBarangMasuk::updateOrCreate(
                ['stock_in_id' => $stockIn4->id, 'gudang_barang_id' => $gudangRouter->id]
            );
        }

        $this->command->info('✓ Barang Masuk, GudangBarang, & Serial Number berhasil di-seed.');

        // ==========================================
        // 9. PINDAH TOKO (MUTASI ANTAR CABANG)
        // ==========================================
        // Mutasi 1: Selesai / Diterima (Status 2)
        $pindah1 = PindahGudang::updateOrCreate(
            ['no_ref' => 'MUT-2026-001'],
            [
                'date' => Carbon::now()->subDays(7)->toDateString(),
                'from' => $toko1->id,
                'to' => $toko2->id,
                'status' => 2, // Diterima
            ]
        );

        DetailBarangKeluar::updateOrCreate(
            ['pindah_gudang_id' => $pindah1->id, 'barang_id' => $barang4->id],
            [
                'gudang_barang_id' => $gudangMouse->id,
                'serial_number_id' => 0,
                'status' => 2,
                'keterangan' => 'Transfer 2 unit Mouse ke Cabang Selatan (Selesai)',
            ]
        );

        // Mutasi 2: Sedang Dikirim / Menunggu Penerimaan (Status 1)
        $pindah2 = PindahGudang::updateOrCreate(
            ['no_ref' => 'MUT-2026-002'],
            [
                'date' => Carbon::now()->subDays(1)->toDateString(),
                'from' => $toko1->id,
                'to' => $toko3->id,
                'status' => 1, // Sedang Dikirim / Menunggu Konfirmasi Masuk
            ]
        );

        DetailBarangKeluar::updateOrCreate(
            ['pindah_gudang_id' => $pindah2->id, 'barang_id' => $barang1->id],
            [
                'gudang_barang_id' => $gudang1->id,
                'serial_number_id' => $gudang1->serial_number_id ?? 1,
                'status' => 1,
                'keterangan' => 'Transfer 1 unit ROG ke Cabang Barat (Dalam Perjalanan)',
            ]
        );

        $this->command->info('✓ Data Pindah Toko (In & Out) berhasil dibuat.');

        // ==========================================
        // 10. PURCHASE ORDER (PO)
        // ==========================================
        // PO 1: Selesai & Diterima
        $po1 = Po::updateOrCreate(
            ['kode_po' => 'PO-2026-001'],
            [
                'date' => Carbon::now()->subDays(25)->toDateString(),
                'supplier_id' => $supplier1->id,
                'nama_purchase' => 'Citra (Manager)',
                'nama_supplier' => $supplier1->nama_supplier,
                'alamat_supplier' => $supplier1->alamat,
                'telepon' => '081122334455',
                'toko_id' => $toko1->id,
                'nama_sales' => 'Hendra Setiawan',
                'subtotal' => 72000000,
                'po_dp' => 20000000,
                'ppn' => 7920000,
                'status' => 1,
                'status_terima' => 2, // Sudah diterima
                'status_bayar' => 1,  // Lunas
                'keterangan' => 'Pengadaan PO Unit Laptop Asus ROG',
                'show_tempo' => 0,
                'jatuh_tempo' => Carbon::now()->addDays(30)->toDateString(),
                'alamat_kirim' => $toko1->alamat_toko,
                'jenis_brang' => 'Unit Komputer',
            ]
        );

        PoDetail::updateOrCreate(
            ['po_id' => $po1->id, 'barang_id' => $barang1->id],
            [
                'jumlah' => 3,
                'discount' => 0,
                'price' => 24000000,
                'subtotal' => 72000000,
            ]
        );

        // PO 2: Draft / Baru
        $po2 = Po::updateOrCreate(
            ['kode_po' => 'PO-2026-002'],
            [
                'date' => Carbon::now()->subDays(2)->toDateString(),
                'supplier_id' => $supplier3->id,
                'nama_purchase' => 'Citra (Manager)',
                'nama_supplier' => $supplier3->nama_supplier,
                'alamat_supplier' => $supplier3->alamat,
                'telepon' => '089988776655',
                'toko_id' => $toko1->id,
                'nama_sales' => 'Rian Pratama',
                'subtotal' => 15500000,
                'po_dp' => 0,
                'ppn' => 0,
                'status' => 2, // Draft / Pending
                'status_terima' => 1, // Belum diterima
                'status_bayar' => 2,  // Belum bayar
                'keterangan' => 'Pengadaan Aksesoris Logitech Tambahan',
                'show_tempo' => 1,
                'jatuh_tempo' => Carbon::now()->addDays(14)->toDateString(),
                'alamat_kirim' => $toko1->alamat_toko,
                'jenis_brang' => 'Aksesoris',
            ]
        );

        PoDetail::updateOrCreate(
            ['po_id' => $po2->id, 'barang_id' => $barang4->id],
            [
                'jumlah' => 10,
                'discount' => 0,
                'price' => 1550000,
                'subtotal' => 15500000,
            ]
        );

        $this->command->info('✓ Data Purchase Order (PO) berhasil dibuat.');

        // ==========================================
        // 11. QUOTATION (PENAWARAN HARGA)
        // ==========================================
        $quote1 = Quotation::updateOrCreate(
            ['kode_quotation' => 'QUO-2026-001'],
            [
                'date' => Carbon::now()->subDays(6)->toDateString(),
                'nama_pembeli' => 'PT Sumber Cahaya Makmur',
                'alamat_pembeli' => 'Gedung Menara Astra Lt. 12, Jakarta',
                'telepon' => '021-5551234',
                'toko_id' => $toko1->id,
                'user_id' => $adminUser->id,
                'nama_sales' => 'Agus (Sales)',
                'subtotal' => 52500000,
                'ppn' => 5775000,
                'status' => 1,
                'keterangan' => 'Penawaran paket pengadaan IT kantor cabang',
                'show_infopembayaran' => 1,
                'show_option' => 0,
                'option_text' => null,
                'show_project' => 1,
                'nama_project' => 'Modernization Workspace 2026',
            ]
        );

        QuotationDetail::updateOrCreate(
            ['quotation_id' => $quote1->id, 'barang_id' => $barang1->id],
            [
                'jumlah' => 1,
                'discount' => 0,
                'price' => 28500000,
                'subtotal' => 28500000,
            ]
        );

        QuotationDetail::updateOrCreate(
            ['quotation_id' => $quote1->id, 'barang_id' => $barang2->id],
            [
                'jumlah' => 1,
                'discount' => 0,
                'price' => 24000000,
                'subtotal' => 24000000,
            ]
        );

        $this->command->info('✓ Data Quotation berhasil dibuat.');

        // ==========================================
        // 12. PENJUALAN (SALES ORDER)
        // ==========================================
        $penjualan1 = Penjualan::updateOrCreate(
            ['kode_penjualan' => 'SO-2026-001'],
            [
                'date' => Carbon::now()->subDays(4)->toDateString(),
                'nama_pembeli' => 'PT Maju Bersama Digital',
                'alamat_pembeli' => 'Rukan Grand Galaxy Blok A No. 10, Bekasi',
                'telepon' => '081399887766',
                'user_id' => $adminUser->id,
                'metode_pembayaran' => 'Transfer Bank',
                'toko_id' => $toko1->id,
                'payment_status' => 'Lunas',
                'discount_type' => 'Nominal',
                'discount_value' => '500000',
                'waktu' => '0',
                'subtotal' => 30050000,
                'total_pembayaran' => 29550000,
                'dp_payment' => 0,
                'nama_sales' => 'Agus (Sales)',
                'ppn' => 0,
                'sisa' => 0,
                'status' => 1,
                'keterangan' => 'Penjualan 1 unit ROG dan 1 unit Mouse Logitech',
                'show_infopembayaran' => '1',
                'show_option' => '0',
                'option_text' => null,
                'show_project' => '0',
                'nama_project' => null,
            ]
        );

        DetailPenjualan::updateOrCreate(
            ['penjualan_id' => $penjualan1->id, 'barang_id' => $barang1->id],
            [
                'serial_number_id' => null,
                'discount' => 500000,
                'price' => 28500000,
            ]
        );

        DetailPenjualan::updateOrCreate(
            ['penjualan_id' => $penjualan1->id, 'barang_id' => $barang4->id],
            [
                'serial_number_id' => null,
                'discount' => 0,
                'price' => 1550000,
            ]
        );

        $this->command->info('✓ Data Penjualan berhasil dibuat.');

        // ==========================================
        // 13. INVOICE
        // ==========================================
        $invoice1 = Invoice::updateOrCreate(
            ['kode_invoice' => 'INV-202609-001'],
            [
                'date' => Carbon::now()->subDays(3)->toDateString(),
                'nama_pembeli' => 'PT Sinergi Data Solusi',
                'alamat_pembeli' => 'Sudirman Central Business District (SCBD) Lot 9, Jakarta',
                'tlp_pembeli' => '081298765432',
                'metode_pembayaran' => 'Transfer Bank BCA',
                'toko_id' => $toko1->id,
                'payment_status' => 'Lunas',
                'discount_type' => 'Tidak Ada',
                'discount_value' => '0',
                'waktu' => '0',
                'subtotal' => 28500000,
                'total_pembayaran' => 28500000,
                'dp_payment' => 0,
                'nama_sales' => 'Agus (Sales)',
                'ppn' => 0,
                'sisa' => 0,
                'status' => '1',
                'keterangan' => 'Invoice Pembelian Unit Laptop Asus ROG',
                'show_infopembayaran' => '1',
                'show_option' => '0',
                'option_text' => null,
                'show_project' => '1',
                'nama_project' => 'Project Alpha Scoping',
            ]
        );

        InvoiceDetail::updateOrCreate(
            ['invoice_id' => $invoice1->id, 'barang_id' => $barang1->id],
            [
                'jumlah' => 1,
                'discount' => 0,
                'price' => 28500000,
                'subtotal' => 28500000,
            ]
        );

        // Invoice 2: Belum Lunas (Tempo)
        $invoice2 = Invoice::updateOrCreate(
            ['kode_invoice' => 'INV-202609-002'],
            [
                'date' => Carbon::now()->subDays(1)->toDateString(),
                'nama_pembeli' => 'Klinik Medika Pratama',
                'alamat_pembeli' => 'Jl. Kesehatan No. 4, Jakarta Barat',
                'tlp_pembeli' => '087811223344',
                'metode_pembayaran' => 'Tempo 30 Hari',
                'toko_id' => $toko2->id,
                'payment_status' => 'Belum Lunas',
                'discount_type' => 'Tidak Ada',
                'discount_value' => '0',
                'waktu' => '30',
                'subtotal' => 24000000,
                'total_pembayaran' => 24000000,
                'dp_payment' => 10000000,
                'nama_sales' => 'Admin Utama',
                'ppn' => 0,
                'sisa' => 14000000,
                'status' => '1',
                'keterangan' => 'Invoice Unit ThinkPad untuk Dokter Spesialis',
                'show_infopembayaran' => '1',
                'show_option' => '0',
                'option_text' => null,
                'show_project' => '0',
                'nama_project' => null,
            ]
        );

        InvoiceDetail::updateOrCreate(
            ['invoice_id' => $invoice2->id, 'barang_id' => $barang2->id],
            [
                'jumlah' => 1,
                'discount' => 0,
                'price' => 24000000,
                'subtotal' => 24000000,
            ]
        );

        $this->command->info('✓ Data Invoice berhasil dibuat.');

        // ==========================================
        // 14. BARANG KELUAR (LOGISTIK & SURAT JALAN)
        // ==========================================
        $keluar1 = BarangKeluar::updateOrCreate(
            ['kode_barang_keluar' => 'BK-2026-001'],
            [
                'date' => Carbon::now()->subDays(2)->toDateString(),
                'nama_penerima' => 'Pak Joko - Logistik PT Maju Bersama',
                'alamat_penerima' => 'Rukan Grand Galaxy Blok A No. 10, Bekasi',
                'telepon_penerima' => '081399887766',
                'toko_id' => $toko1->id,
                'waktu' => '09:30',
                'nama_sales' => 'Agus (Sales)',
                'status' => 1, // Done
                'keterangan' => 'Pengiriman barang sesuai SO-2026-001 via kurir internal',
            ]
        );

        DataBarangKeluar::updateOrCreate(
            ['barang_keluar_id' => $keluar1->id, 'barang_id' => $barang1->id],
            [
                'gudang_barang_id' => $gudang1->id,
                'discount' => 0,
                'price' => 28500000,
            ]
        );

        $this->command->info('✓ Data Barang Keluar berhasil dibuat.');

        $this->command->info('======================================================');
        $this->command->info('SEMUA SEEDING SELESAI BERHASIL DENGAN LENGKAP!');
        $this->command->info('Akun Login: admin@admin.com / password');
        $this->command->info('======================================================');
    }
}
