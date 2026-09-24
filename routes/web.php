<?php

use App\Models\Kategori;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::prefix('kategori')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\KategoriController::class, 'index'])->name('kategori');
    Route::post('/', [App\Http\Controllers\KategoriController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\KategoriController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\KategoriController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\KategoriController::class, 'update']);
    Route::get('/select', [App\Http\Controllers\KategoriController::class, 'select']);
    Route::get('/check', [App\Http\Controllers\KategoriController::class, 'check']);
});

Route::prefix('toko')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TokoController::class, 'index'])->name('Toko');
    Route::post('/', [App\Http\Controllers\TokoController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\TokoController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\TokoController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\TokoController::class, 'update']);
    Route::get('/select', [App\Http\Controllers\TokoController::class, 'select']);
    Route::get('/except/{id}', [App\Http\Controllers\TokoController::class, 'except']);
});

Route::prefix('barang')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\BarangController::class, 'index'])->name('Barang');
    Route::get('/get/{id}', [App\Http\Controllers\BarangController::class, 'edit']);
    Route::post('/', [App\Http\Controllers\BarangController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\BarangController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\BarangController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\BarangController::class, 'update']);
    Route::get('/selectmerk', [App\Http\Controllers\BarangController::class, 'selectmerk']);
    Route::get('/select', [App\Http\Controllers\BarangController::class, 'select']);
    Route::get('/toko/{id}', [App\Http\Controllers\BarangController::class, 'toko']);
    Route::post('/get-total-barang-by-toko', [App\Http\Controllers\BarangController::class, 'getTotalBarangByToko']);
    Route::post('/get-total-barang-by-toko-new', [App\Http\Controllers\BarangController::class, 'getTotalBarangByTokoNew']);
    Route::post('/stock', [App\Http\Controllers\BarangController::class, 'stock']);
    Route::get('/{id}', [App\Http\Controllers\BarangController::class, 'get']);
});

Route::prefix('stock')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('Stock');
    Route::get('/get/{id}', [App\Http\Controllers\StockController::class, 'edit']);
    Route::post('/get-sn/{id}', [App\Http\Controllers\StockController::class, 'get']);
    Route::post('/', [App\Http\Controllers\StockController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\StockController::class, 'show']);
    Route::post('/sn/delete', [App\Http\Controllers\StockController::class, 'destroy']);
    Route::post('/sn/update', [App\Http\Controllers\StockController::class, 'update']);
    Route::get('/sn/{id}', [App\Http\Controllers\StockController::class, 'serialNumber']);
});

Route::prefix('stock-in')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\StockInController::class, 'index'])->name('StockIn');
    Route::get('/get/{id}', [App\Http\Controllers\StockInController::class, 'edit']);
    Route::get('/get-sn/{id}', [App\Http\Controllers\StockInController::class, 'getSerialNumber']);
    Route::post('/', [App\Http\Controllers\StockInController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\StockInController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\StockInController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\StockInController::class, 'update']);
    Route::get('/create', [App\Http\Controllers\StockInController::class, 'create']);
    Route::post('/price-list', [App\Http\Controllers\StockInController::class, 'getPriceListBarang']);
    Route::post('/duplicate', [App\Http\Controllers\StockInController::class, 'duplicate']);
    Route::get('/create/po/{id}', [App\Http\Controllers\StockInController::class, 'createPo']);
    Route::post('/insert-po/{id}', [App\Http\Controllers\StockInController::class, 'insert_po']);
});

Route::prefix('pindah-toko')->middleware('auth')->group(function () {
    Route::get('/in', [App\Http\Controllers\PindahTokoController::class, 'in']);
    Route::get('/out', [App\Http\Controllers\PindahTokoController::class, 'out']);
    Route::get('/out/create', [App\Http\Controllers\PindahTokoController::class, 'create']);
    Route::get('/out/edit/{id}', [App\Http\Controllers\PindahTokoController::class, 'edit']);
    Route::get('/out/{id}', [App\Http\Controllers\PindahTokoController::class, 'show']);
    Route::post('/out/update/{id}', [App\Http\Controllers\PindahTokoController::class, 'update']);
    Route::get('/out/delete-detail/{id}', [App\Http\Controllers\PindahTokoController::class, 'destroy_detail']);
    Route::get('/data-out', [App\Http\Controllers\PindahTokoController::class, 'dataOut']);
    Route::get('/data-in', [App\Http\Controllers\PindahTokoController::class, 'dataIn']);
    Route::post('/', [App\Http\Controllers\PindahTokoController::class, 'store']);
    Route::post('/barang/{id}', [App\Http\Controllers\PindahTokoController::class, 'barang']);
    Route::post('/delete', [App\Http\Controllers\PindahTokoController::class, 'destroy']);
    Route::post('/terima', [App\Http\Controllers\PindahTokoController::class, 'terima']);
    Route::get('/out/print/{id}', [App\Http\Controllers\PindahTokoController::class, 'outDownload']);
    Route::get('/in/print/{id}', [App\Http\Controllers\PindahTokoController::class, 'inDownload']);
});

Route::prefix('serial-number')->middleware('auth')->group(function () {
    Route::get('/id_toko/{id}/id_barang/{idbarang}', [App\Http\Controllers\SerialNumberController::class, 'show']);
});

Route::prefix('users')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('', [App\Http\Controllers\UserController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\UserController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\UserController::class, 'destroy']);
    Route::get('/get/{id}', [App\Http\Controllers\UserController::class, 'edit']);
    Route::post('/update', [App\Http\Controllers\UserController::class, 'update']);
    Route::get('/select', [App\Http\Controllers\UserController::class, 'select']);
});

Route::prefix('signature')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\SignatureController::class, 'index'])->name('signature');
    Route::post('/', [App\Http\Controllers\SignatureController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\SignatureController::class, 'show']);
    Route::get('/get/{id}', [App\Http\Controllers\SignatureController::class, 'get']);
    Route::post('/delete', [App\Http\Controllers\SignatureController::class, 'destroy']);
});
Route::prefix('penjualan')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\PenjualanController::class, 'index']);
    Route::get('/create', [App\Http\Controllers\PenjualanController::class, 'create']);
    Route::get('/create/{id}', [App\Http\Controllers\PenjualanController::class, 'convert']);
    Route::get('/convert-invoice/{id}', [App\Http\Controllers\PenjualanController::class, 'convert_invoice']);
    Route::post('', [App\Http\Controllers\PenjualanController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\PenjualanController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\PenjualanController::class, 'destroy']);
    Route::post('/delete-barang', [App\Http\Controllers\PenjualanController::class, 'destroyBarang']);
    Route::get('/edit/{id}', [App\Http\Controllers\PenjualanController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\PenjualanController::class, 'update']);
    Route::post('/barang/{id}', [App\Http\Controllers\PenjualanController::class, 'barang']);
    Route::get('/print/{id}', [App\Http\Controllers\PenjualanController::class, 'download']);
    Route::get('/detail/{id}', [App\Http\Controllers\PenjualanController::class, 'get']);
    Route::get('/surat-jalan/{id}', [App\Http\Controllers\PenjualanController::class, 'surat_jalan']);
});

Route::prefix('barang-keluar')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\BarangKeluarController::class, 'index']);
    Route::get('/create', [App\Http\Controllers\BarangKeluarController::class, 'create']);
    Route::get('/create/{id}', [App\Http\Controllers\BarangKeluarController::class, 'convert']);
    Route::get('/convert-invoice/{id}', [App\Http\Controllers\BarangKeluarController::class, 'convert_invoice']);
    Route::post('', [App\Http\Controllers\BarangKeluarController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\BarangKeluarController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\BarangKeluarController::class, 'destroy']);
    Route::post('/delete-barang', [App\Http\Controllers\BarangKeluarController::class, 'destroyBarang']);
    Route::get('/edit/{id}', [App\Http\Controllers\BarangKeluarController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\BarangKeluarController::class, 'update']);
    Route::post('/barang/{id}', [App\Http\Controllers\BarangKeluarController::class, 'barang']);
    Route::get('/print/{id}', [App\Http\Controllers\BarangKeluarController::class, 'download']);
    Route::get('/detail/{id}', [App\Http\Controllers\BarangKeluarController::class, 'get']);
    Route::get('/surat-jalan/{id}', [App\Http\Controllers\BarangKeluarController::class, 'surat_jalan']);
});

Route::prefix('quotation')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\QuotationController::class, 'index']);
    Route::get('/create', [App\Http\Controllers\QuotationController::class, 'create']);
    Route::post('', [App\Http\Controllers\QuotationController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\QuotationController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\QuotationController::class, 'destroy']);
    Route::post('/delete-barang', [App\Http\Controllers\QuotationController::class, 'destroyBarang']);
    Route::get('/edit/{id}', [App\Http\Controllers\QuotationController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\QuotationController::class, 'update']);
    Route::post('/barang/{id}', [App\Http\Controllers\QuotationController::class, 'barang']);
    Route::get('/print/{id}', [App\Http\Controllers\QuotationController::class, 'print']);
    Route::get('/detail/{id}', [App\Http\Controllers\QuotationController::class, 'get']);
    Route::post('/duplicate', [App\Http\Controllers\QuotationController::class, 'duplicate']);
});

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\SerialNumberController::class, 'index']);
});

Route::prefix('report')->middleware('auth')->group(function () {
    Route::get('/barang-masuk', [App\Http\Controllers\ReportController::class, 'index']);
    Route::post('/barang-masuk', [App\Http\Controllers\ReportController::class, 'get_barang_masuk']);
    Route::post('/download-barang-masuk', [App\Http\Controllers\ReportController::class, 'export_barang_masuk']);
    Route::get('/pindah-barang', [App\Http\Controllers\ReportController::class, 'pindah_barang']);
    Route::post('/get-pindah-barang', [App\Http\Controllers\ReportController::class, 'get_pindah_barang']);
    Route::post('/download-pindah-barang', [App\Http\Controllers\ReportController::class, 'export_pindah_barang']);
    Route::get('/stock', [App\Http\Controllers\ReportController::class, 'barang_stock']);
    Route::post('/get-stock', [App\Http\Controllers\ReportController::class, 'get_stock']);
    Route::post('/download-stock', [App\Http\Controllers\ReportController::class, 'export_stock']);
    Route::get('/laba-rugi', [App\Http\Controllers\ReportController::class, 'laba_rugi']);
    Route::post('/get-laba-rugi', [App\Http\Controllers\ReportController::class, 'get_laba_rugi']);
    Route::post('/download-laba-rugi', [App\Http\Controllers\ReportController::class, 'export_laba_rugi']);
    Route::get('/penjualan', [App\Http\Controllers\ReportController::class, 'barang_keluar']);
    Route::post('/penjualan', [App\Http\Controllers\ReportController::class, 'get_barang_keluar']);
    Route::post('/download-penjualan', [App\Http\Controllers\ReportController::class, 'export_barang_keluar']);
    Route::get('/po', [App\Http\Controllers\ReportController::class, 'barang_po']);
    Route::post('/po', [App\Http\Controllers\ReportController::class, 'get_barang_po']);
    Route::post('/download-po', [App\Http\Controllers\ReportController::class, 'export_barang_po']);
});

Route::prefix('invoice')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\InvoiceController::class, 'index']);
    Route::get('/create', [App\Http\Controllers\InvoiceController::class, 'create']);
    Route::get('/create/{id}', [App\Http\Controllers\InvoiceController::class, 'convert']);
    Route::post('', [App\Http\Controllers\InvoiceController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\InvoiceController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\InvoiceController::class, 'destroy']);
    Route::post('/delete-barang', [App\Http\Controllers\InvoiceController::class, 'destroyBarang']);
    Route::get('/edit/{id}', [App\Http\Controllers\InvoiceController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\InvoiceController::class, 'update']);
    Route::post('/barang/{id}', [App\Http\Controllers\InvoiceController::class, 'barang']);
    Route::get('/print/{id}', [App\Http\Controllers\InvoiceController::class, 'print']);
    Route::get('/detail/{id}', [App\Http\Controllers\InvoiceController::class, 'get']);
});

Route::prefix('po')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\PoController::class, 'index']);
    Route::get('/create', [App\Http\Controllers\PoController::class, 'create']);
    Route::post('', [App\Http\Controllers\PoController::class, 'store']);
    Route::get('/show', [App\Http\Controllers\PoController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\PoController::class, 'destroy']);
    Route::post('/delete-barang', [App\Http\Controllers\PoController::class, 'destroyBarang']);
    Route::get('/edit/{id}', [App\Http\Controllers\PoController::class, 'edit']);
    Route::post('/update/{id}', [App\Http\Controllers\PoController::class, 'update']);
    Route::post('/barang/{id}', [App\Http\Controllers\PoController::class, 'barang']);
    Route::get('/print/{id}', [App\Http\Controllers\PoController::class, 'print']);
    Route::get('/detail/{id}', [App\Http\Controllers\PoController::class, 'get']);
    Route::get('/terima/{id}', [App\Http\Controllers\PoController::class, 'terima']);
    Route::get('/select', [App\Http\Controllers\PoController::class, 'select']);
});

Route::prefix('search')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\SearchController::class, 'index']);
    Route::post('', [App\Http\Controllers\SearchController::class, 'search']);
    Route::get('/select', [App\Http\Controllers\SearchController::class, 'select']);
    Route::get('/selectnama', [App\Http\Controllers\SearchController::class, 'selectnama']);
    Route::post('/searchnama', [App\Http\Controllers\SearchController::class, 'searchnama']);
    Route::post('/download-search-nama', [App\Http\Controllers\SearchController::class, 'export_search_nama']);
});

Route::prefix('setting')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\SettingController::class, 'index']);
    Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('Setting');
    Route::get('/get/{id}', [App\Http\Controllers\SettingController::class, 'edit']);
    Route::post('/', [App\Http\Controllers\SettingController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\SettingController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\SettingController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\SettingController::class, 'update']);    
});

Route::prefix('supplier')->middleware('auth')->group(function () {
    Route::get('', [App\Http\Controllers\SupplierController::class, 'index']);
    Route::get('/', [App\Http\Controllers\SupplierController::class, 'index'])->name('Supplier');
    Route::get('/get/{id}', [App\Http\Controllers\SupplierController::class, 'edit']);
    Route::post('/', [App\Http\Controllers\SupplierController::class, 'store']);
    Route::get('/data', [App\Http\Controllers\SupplierController::class, 'show']);
    Route::post('/delete', [App\Http\Controllers\SupplierController::class, 'destroy']);
    Route::post('/update', [App\Http\Controllers\SupplierController::class, 'update']); 
    Route::get('/select', [App\Http\Controllers\SupplierController::class, 'select']); 
});

use Illuminate\Support\Facades\Artisan;

Route::get('/run-migration/{token}', function ($token) {
    $secretToken = 'berandahukummigrate';
    
    if ($token !== $secretToken) {
        return response('Akses Ditolak: Token Salah', 403);
    }
    
    try {
        Artisan::call('migrate', [
            '--force' => true,
        ]);
        $output = Artisan::output();
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Migration berhasil dijalankan',
            'output'  => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
})->withoutMiddleware(\App\Http\Middleware\CheckBlockedIp::class);


Route::get('/fix-all-migrations', function () {
    // 1. Ambil semua file migration di folder database/migrations
    $files = glob(database_path('migrations/*.php'));
    
    // 2. Ambil batch terbesar saat ini
    $maxBatch = DB::table('migrations')->max('batch') ?? 1;
    
    $inserted = [];
    $skipped = [];

    foreach ($files as $file) {
        $migrationName = basename($file, '.php');

        // Cek apakah migration ini sudah tercatat di tabel migrations
        $exists = DB::table('migrations')->where('migration', $migrationName)->exists();

        if (!$exists) {
            // Masukkan nama migration ini ke tabel migrations agar di-skip oleh Laravel
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $maxBatch
            ]);
            $inserted[] = $migrationName;
        } else {
            $skipped[] = $migrationName;
        }
    }

    return response()->json([
        'status' => 'Berhasil menandai semua migration lama sebagai "sudah dijalankan".',
        'file_yang_ditambahkan_ke_database' => $inserted,
    ]);
});


Route::get('/run-migration-fix', function () {
    $output = [];

    // 1. Composer dump-autoload (membutuhkan fungsi exec/shell_exec PHP di server)
    if (function_exists('exec')) {
        exec('composer dump-autoload 2>&1', $outputComposer, $returnCode);
        $output['1. composer dump-autoload'] = implode("\n", $outputComposer);
    } else {
        $output['1. composer dump-autoload'] = 'Fungsi exec() di-disable oleh server.';
    }

    // 2. php artisan config:clear
    Artisan::call('config:clear');
    $output['2. config:clear'] = Artisan::output();

    // 3. php artisan cache:clear
    Artisan::call('cache:clear');
    $output['3. cache:clear'] = Artisan::output();

    // 4. php artisan migrate
    Artisan::call('migrate');
    $output['4. migrate'] = Artisan::output();

    return response()->json($output);
});