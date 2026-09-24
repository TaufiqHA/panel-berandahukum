# Issue: Bug Missing Column 'harga' pada tabel 'barangs'

**Tanggal:** 15 Mei 2026
**Error Log:**
```text
[2026-05-15 19:43:59] local.ERROR: SQLSTATE[HY000]: General error: 1 table barangs has no column named harga (SQL: insert into "barangs" ("kategori_id", "nama_product", "merk", "satuan", "warna", "berat", "ukuran", "harga", "wajib_serial_number", "keterangan", "updated_at", "created_at") values (1, barang testing, tidak ada, kg, abu abu, 100, 20, 200000, 1, tidak ada, 2026-05-15 19:43:59, 2026-05-15 19:43:59))
```

## Deskripsi Masalah
Terjadi *General error* saat aplikasi mencoba menyimpan data barang baru ke database. Error menyatakan bahwa tabel `barangs` tidak memiliki kolom bernama `harga`. Hal ini terjadi karena ada ketidaksesuaian antara struktur tabel di database dengan data yang dikirim dari form (Controller).

## Langkah-Langkah Perbaikan (Fixing Steps)

Instruksi ini dirancang agar mudah diikuti oleh Junior Developer atau AI.

### Langkah 1: Buat File Migration Baru
Kita perlu menambahkan kolom `harga` ke tabel `barangs` menggunakan fitur Migration bawaan Laravel.
1. Buka terminal/command prompt di direktori proyek (`/home/padi-kering/Documents/KERJA/panel-berandahukum`).
2. Jalankan perintah artisan berikut:
   ```bash
   php artisan make:migration add_harga_to_barangs_table --table=barangs
   ```
3. Perintah di atas akan membuat file baru di folder `database/migrations/`. Buka file terbaru tersebut.

### Langkah 2: Edit File Migration
Tambahkan definisi kolom `harga` pada file migration yang baru saja dibuat.

1. Pada method `up()`, tambahkan kode berikut di dalam *schema builder*:
   ```php
   public function up()
   {
       Schema::table('barangs', function (Blueprint $table) {
           // Menambahkan kolom harga setelah kolom ukuran (atau kolom lain yang sesuai)
           $table->decimal('harga', 15, 2)->default(0)->after('ukuran'); 
           // Catatan: Gunakan tipe data 'decimal' atau 'bigInteger' untuk nominal uang. 
           // Jika aplikasi sebelumnya menggunakan integer, ubah menjadi ->integer('harga')->default(0);
       });
   }
   ```
2. Pada method `down()`, tambahkan kode untuk menghapus kolom (untuk keperluan rollback):
   ```php
   public function down()
   {
       Schema::table('barangs', function (Blueprint $table) {
           $table->dropColumn('harga');
       });
   }
   ```

### Langkah 3: Eksekusi Migration
Terapkan perubahan skema ke database.
1. Kembali ke terminal.
2. Jalankan perintah:
   ```bash
   php artisan migrate
   ```

### Langkah 4: Verifikasi Model (Opsional tapi Penting)
Pastikan kolom `harga` sudah diizinkan untuk diisi secara massal (Mass Assignment).
1. Buka file `app/Models/Barang.php`.
2. Pastikan `harga` ada di dalam array `$fillable`. (Berdasarkan pengecekan sebelumnya, ini sepertinya sudah ada, tapi pastikan kembali).
   ```php
   protected $fillable = ['kategori_id', 'nama_product','merk','satuan', 'warna', 'berat', 'ukuran', 'harga','keterangan', 'wajib_serial_number'];
   ```

### Langkah 5: Testing Ulang
1. Buka aplikasi di browser.
2. Coba tambahkan data barang baru dengan mengisi nominal harga.
3. Pastikan data berhasil tersimpan tanpa memunculkan pesan error 500.