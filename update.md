# Update Log & Perubahan Project

Dokumen ini mencatat perubahan file antara project saat ini dengan versi lama (`project lama`) per tanggal 17 April 2026.

## Ringkasan Perubahan Utama
1.  **Sistem Tanda Tangan Digital**: Penambahan fitur tanda tangan (Gambar/Upload) yang terintegrasi ke seluruh dokumen cetak.
2.  **Standardisasi Master Sales & Purchasing**: Perubahan input `nama_sales` dan `nama_purchasing` menjadi sistem dropdown yang terhubung dengan model `Signature` di seluruh modul transaksi (Invoice, Quotation, Penjualan, PO, Barang Keluar) dan Master Supplier. Ini memastikan integritas data tanda tangan digital.
3.  **Update Template PDF**: Penyesuaian seluruh file `print.blade.php` (Invoice, PO, Quotation, Penjualan, Barang Keluar, Pindah Toko) untuk menampilkan tanda tangan dinamis.
4.  **Database & Migrasi**: Penambahan tabel `signatures` (untuk manajemen tanda tangan terpisah dari users) serta penambahan kolom pendukung pada tabel `users`.

---

## Daftar File yang Berubah

### 1. File Baru ([NEW])
- `app/Http/Controllers/SignatureController.php` (Logika Tanda Tangan)
- `app/Models/Signature.php` (Model Tanda Tangan)
- `resources/views/master/signature.blade.php` (UI Tanda Tangan)
- `database/migrations/2026_04_17_..._create_signatures_table.php`
- `database/migrations/2026_04_17_135221_add_signature_to_users_table.php`
- `database/migrations/2026_04_17_142713_add_missing_columns_to_users_table.php`
- `database/seeders/UserSeeder.php`

### 2. File Modifikasi ([MODIFY])

#### **Modul Transaksi (Standardisasi Input Nama Sales/Purchasing)**
- **Invoice**: `create.blade.php`, `edit.blade.php`, `show.blade.php`, `convert.blade.php`
- **Quotation**: `create.blade.php`, `edit.blade.php`, `show.blade.php`
- **Penjualan (Sales Order)**: `create.blade.php`, `edit.blade.php`, `show.blade.php`, `convert.blade.php`, `convert-invoice.blade.php`
- **Purchase Order (PO)**: `create.blade.php`, `edit.blade.php`, `show.blade.php`, `terima.blade.php`
- **Barang Keluar**: `create.blade.php`, `edit.blade.php`, `show.blade.php`, `convert.blade.php`, `convert-invoice.blade.php`

#### **Master Data**
- `resources/views/master/supplier.blade.php` (Update modal Create/Edit)
- `resources/views/master/toko.blade.php`

#### **Views (Print Templates & PDF)**
- `resources/views/invoice/print.blade.php`
- `resources/views/pindah-toko/barang-keluar/print.blade.php`
- `resources/views/po/print.blade.php`
- `resources/views/quotation/print.blade.php`
- `resources/views/penjualan/print.blade.php`
- `resources/views/barang-keluar/print.blade.php`
- `resources/views/penjualan/surat-jalan.blade.php`
- `resources/views/barang-keluar/surat-jalan.blade.php`

#### **Core & Logic**
- `routes/web.php`
- `app/Http/Controllers/InvoiceController.php`
- `app/Http/Controllers/PoController.php`
- `app/Http/Controllers/QuotationController.php`
- `app/Http/Controllers/PenjualanController.php`
- `app/Http/Controllers/BarangKeluarController.php`
- `app/Models/User.php`
- `resources/views/layouts/app.blade.php`

---

## Panduan Deployment
> [!IMPORTANT]
> Jangan lupa menjalankan perintah berikut setelah file diunggah ke server:
> 1. `php artisan migrate` (Wajib untuk menambahkan tabel dan kolom baru).
> 2. `php artisan db:seed --class=UserSeeder` (Opsional).
> 3. `chmod -R 775 storage bootstrap/cache` (Pastikan permission folder benar).
