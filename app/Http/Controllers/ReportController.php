<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\BarangKeluar;
use App\Models\DetailBarangKeluar;
use App\Models\GudangBarang;
use App\Models\Penjualan;
use App\Models\Po;
use App\Models\StockIn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function barangMasuk(array $filters): array
    {
        [$from, $to] = $this->range($filters);
        $user = Auth::user();

        $query = StockIn::with(['barang', 'toko'])->whereBetween('tanggal_masuk', [$from, $to]);

        if (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_barang'])) {
            $query->whereIn('barang_id', (array) $filters['nama_barang']);
        }

        if ($user && (int) $user->status === 2) {
            $query->where('toko_id', $user->toko_id);
        }

        $rows = [];
        $no = 1;

        foreach ($query->orderBy('tanggal_masuk', 'desc')->get() as $stock) {
            if (empty($stock->barang)) {
                continue;
            }

            $rows[] = [
                $no++,
                $stock->barang->nama_product,
                $stock->jumlah,
                $stock->tanggal_masuk ? date('d F Y', strtotime($stock->tanggal_masuk)) : '',
                number_format((float) $stock->harga_beli),
                number_format((float) $stock->harga_jual),
                number_format((float) $stock->price_list),
                $stock->made_in,
                $stock->supplier,
                $stock->toko?->nama_toko,
                $stock->keterangan,
            ];
        }

        return [
            'headings' => ['No', 'Nama Barang', 'Jumlah', 'Tanggal Masuk', 'Harga Beli', 'Harga Jual', 'Price List', 'Made In', 'Supplier', 'Toko', 'Keterangan'],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function penjualan(array $filters): array
    {
        [$from, $to] = $this->range($filters);

        $query = Penjualan::with([
            'toko',
            'detail_penjualan.serial_number',
            'detail_penjualan.gudang_barang.serial_number',
            'detail_penjualan.gudang_barang.barang',
        ])->whereBetween('date', [$from, $to]);

        if (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_barang'])) {
            $query->whereIn('id', function ($sub) use ($filters): void {
                $sub->select('penjualan_id')
                    ->from('detail_penjualans')
                    ->whereIn('barang_id', (array) $filters['nama_barang'])
                    ->groupBy('penjualan_id');
            });
        }

        $rows = [];
        $no = 1;

        foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
            foreach ($penjualan->detail_penjualan as $detail) {
                $serial = $detail->serial_number->serial_number
                    ?? $detail->gudang_barang?->serial_number?->serial_number
                    ?? '';

                $rows[] = [
                    $no++,
                    $penjualan->kode_penjualan,
                    $detail->gudang_barang?->barang?->nama_product,
                    $serial,
                    $penjualan->toko?->nama_toko,
                    $penjualan->date ? date('d F Y', strtotime($penjualan->date)) : '',
                    $penjualan->nama_pembeli,
                    $penjualan->alamat_pembeli,
                    $penjualan->telepon,
                    (float) $detail->price - ((float) $detail->price * (float) $detail->discount / 100),
                    $penjualan->nama_sales,
                ];
            }
        }

        return [
            'headings' => ['No', 'No Ref', 'Nama Barang', 'Serial Number', 'Nama Toko', 'Tanggal Keluar', 'Nama Pembeli', 'Alamat Pembeli', 'No Telepon', 'Harga Terjual', 'Sales'],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function barangKeluar(array $filters): array
    {
        [$from, $to] = $this->range($filters);

        $query = BarangKeluar::with([
            'toko',
            'data_barang_keluar.barang',
            'data_barang_keluar.gudang_barang.serial_number',
        ])->whereBetween('date', [$from, $to]);

        if (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_barang'])) {
            $query->whereIn('id', function ($sub) use ($filters): void {
                $sub->select('barang_keluar_id')
                    ->from('data_barang_keluars')
                    ->whereIn('barang_id', (array) $filters['nama_barang'])
                    ->groupBy('barang_keluar_id');
            });
        }

        $rows = [];
        $no = 1;

        foreach ($query->orderBy('date', 'desc')->get() as $barangKeluar) {
            foreach ($barangKeluar->data_barang_keluar as $detail) {
                $serial = $detail->gudang_barang?->serial_number?->serial_number ?? '';
                $harga = (float) $detail->price - ((float) $detail->price * (float) $detail->discount / 100);

                $rows[] = [
                    $no++,
                    $barangKeluar->kode_barang_keluar,
                    $detail->barang?->nama_product,
                    $serial,
                    $barangKeluar->toko?->nama_toko,
                    $barangKeluar->date ? date('d F Y', strtotime($barangKeluar->date)) : '',
                    $barangKeluar->nama_penerima,
                    $harga,
                    $barangKeluar->keterangan,
                ];
            }
        }

        return [
            'headings' => ['No', 'No Ref', 'Nama Barang', 'Serial Number', 'Nama Toko', 'Tanggal Keluar', 'Nama Penerima', 'Harga', 'Keterangan'],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function pindahBarang(array $filters): array
    {
        [$from, $to] = $this->range($filters);
        $user = Auth::user();

        $tokoFrom = $filters['toko_from'] ?? null;
        $tokoTo = $filters['toko_to'] ?? null;

        if ($user && (int) $user->status === 2) {
            $tokoFrom = $user->toko_id;
        }

        $query = DetailBarangKeluar::with([
            'pindah_gudang.toko',
            'pindah_gudang.toko_to',
            'barang',
            'gudang_barang.serial_number',
            'gudang_barang.detail_barang_masuk.stock_in',
        ])->whereHas('pindah_gudang', function ($q) use ($from, $to, $tokoFrom, $tokoTo): void {
            $q->whereBetween('date', [$from, $to]);

            if ($tokoFrom) {
                $q->where('from', $tokoFrom);
            }

            if ($tokoTo) {
                $q->where('to', $tokoTo);
            }
        });

        $rows = [];
        $no = 1;

        foreach ($query->get() as $value) {
            $rows[] = [
                $no++,
                $value->pindah_gudang?->date ? date('d F Y', strtotime($value->pindah_gudang->date)) : '',
                $value->pindah_gudang?->no_ref,
                $value->pindah_gudang?->toko?->nama_toko,
                $value->pindah_gudang?->toko_to?->nama_toko,
                $value->barang?->nama_product,
                $value->gudang_barang?->serial_number?->serial_number ?? '',
                $value->keterangan,
                (int) $value->status === 1 ? 'Dikirim' : 'Diterima',
                $value->gudang_barang?->detail_barang_masuk?->stock_in?->harga_beli ?? '',
            ];
        }

        return [
            'headings' => ['No', 'Tanggal', 'Ref Number', 'Toko Awal', 'Toko Tujuan', 'Nama Barang', 'Serial Number', 'Keterangan', 'Status', 'Harga Beli'],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function stock(array $filters): array
    {
        $user = Auth::user();

        $query = GudangBarang::with(['barang', 'serial_number', 'detail_barang_masuk.stock_in', 'toko']);

        if ($user && (int) $user->status === 2) {
            $query->where('toko_id', $user->toko_id);
        } elseif (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_barang'])) {
            $query->whereIn('barang_id', (array) $filters['nama_barang']);
        }

        $rows = [];
        $no = 1;

        foreach ($query->get() as $unit) {
            $stockIn = $unit->detail_barang_masuk?->stock_in;

            $rows[] = [
                $no++,
                $unit->detail_barang_masuk?->stock_in?->tanggal_masuk ? date('d F Y', strtotime($unit->detail_barang_masuk->stock_in->tanggal_masuk)) : '',
                $stockIn?->po?->kode_po ?? '',
                $stockIn?->po?->date ? date('d F Y', strtotime($stockIn->po->date)) : '',
                $unit->barang?->nama_product,
                $unit->serial_number?->serial_number ?? '',
                1,
                $unit->barang?->warna,
                $unit->toko?->nama_toko,
                $stockIn?->harga_beli ?? '',
                $stockIn?->harga_jual ?? '',
                $stockIn?->supplier ?? '',
            ];
        }

        return [
            'headings' => ['No', 'Tanggal Masuk', 'No PO', 'Tanggal PO', 'Nama Barang', 'Serial Number', 'Jumlah', 'Warna', 'Nama Toko', 'Harga Beli', 'Harga Jual', 'Supplier'],
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function labaRugi(array $filters): array
    {
        [$from, $to] = $this->range($filters);
        $jenis = (string) ($filters['jenis_report'] ?? '1');

        $query = Penjualan::with([
            'toko',
            'detail_penjualan.serial_number',
            'detail_penjualan.gudang_barang.serial_number',
            'detail_penjualan.gudang_barang.detail_barang_masuk.stock_in',
            'detail_penjualan.barang',
        ])->whereBetween('date', [$from, $to]);

        if (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_barang'])) {
            $query->whereIn('id', function ($sub) use ($filters): void {
                $sub->select('penjualan_id')
                    ->from('detail_penjualans')
                    ->whereIn('barang_id', (array) $filters['nama_barang'])
                    ->groupBy('penjualan_id');
            });
        }

        $rows = [];
        $no = 1;

        if ($jenis === '2') {
            $headings = ['No', 'Tanggal', 'Kode Penjualan', 'Nama Pembeli', 'Metode Pembayaran', 'Nama Toko', 'Cara Pembayaran', 'Total Pembayaran', 'DP', 'Sisa', 'Total Hrg.Beli', 'Keuntungan', 'Status', 'Nama Project'];
            $totalHrgBeli = $totalPembayaran = $totalDp = $totalSisa = $totalUntung = 0;

            foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
                $hargaBeli = 0;
                foreach ($penjualan->detail_penjualan as $detail) {
                    $hargaBeli += (float) ($detail->gudang_barang?->detail_barang_masuk?->stock_in?->harga_beli ?? 0);
                }

                $total = (float) $penjualan->subtotal;
                if ((float) $penjualan->ppn != 0) {
                    $total += $total * 11 / 100;
                }

                $untung = $total - $hargaBeli;
                $totalPembayaran += $total;
                $totalHrgBeli += $hargaBeli;
                $totalDp += (float) $penjualan->dp_payment;
                $totalSisa += (float) $penjualan->sisa;
                $totalUntung += $untung;

                $rows[] = [
                    $no++,
                    $penjualan->date ? date('d F Y', strtotime($penjualan->date)) : '',
                    $penjualan->kode_penjualan,
                    $penjualan->nama_pembeli,
                    $penjualan->metode_pembayaran,
                    $penjualan->toko?->nama_toko,
                    $penjualan->payment_status,
                    $total,
                    (float) $penjualan->dp_payment,
                    (float) $penjualan->sisa,
                    $hargaBeli,
                    $untung,
                    (int) $penjualan->status === 2 ? 'Draft' : 'Done',
                    $penjualan->nama_project ?? '',
                ];
            }

            $rows[] = ['', 'TOTAL', '', '', '', '', '', $totalPembayaran, $totalDp, $totalSisa, $totalHrgBeli, $totalUntung, '', ''];

            return ['headings' => $headings, 'rows' => $rows];
        }

        $headings = ['No', 'Tanggal', 'No Ref', 'Nama Barang', 'Serial Number', 'Nama Toko', 'Harga Beli', 'Harga Jual', 'Keuntungan'];
        $totalHrgBeli = $totalHrgJual = $totalUntung = 0;

        foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
            foreach ($penjualan->detail_penjualan as $detail) {
                $hargaBeli = (float) ($detail->gudang_barang?->detail_barang_masuk?->stock_in?->harga_beli ?? 0);
                $hargaJual = (float) $detail->price - ((float) $detail->price * (float) $detail->discount / 100);
                $untung = $hargaJual - $hargaBeli;

                $serial = $detail->serial_number->serial_number
                    ?? $detail->gudang_barang?->serial_number?->serial_number
                    ?? '';

                $totalHrgBeli += $hargaBeli;
                $totalHrgJual += $hargaJual;
                $totalUntung += $untung;

                $rows[] = [
                    $no++,
                    $penjualan->date ? date('d F Y', strtotime($penjualan->date)) : '',
                    $penjualan->kode_penjualan,
                    $detail->barang?->nama_product,
                    $serial,
                    $penjualan->toko?->nama_toko,
                    $hargaBeli,
                    $hargaJual,
                    $untung,
                ];
            }
        }

        $rows[] = ['', 'TOTAL', '', '', '', '', $totalHrgBeli, $totalHrgJual, $totalUntung];

        return ['headings' => $headings, 'rows' => $rows];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function po(array $filters): array
    {
        [$from, $to] = $this->range($filters);
        $user = Auth::user();

        $query = Po::with(['toko', 'supplier'])
            ->where('status', 1)
            ->whereBetween('date', [$from, $to]);

        if (! empty($filters['jatuh_tempo_awal']) && ! empty($filters['jatuh_tempo_akhir'])) {
            $query->whereBetween('jatuh_tempo', [
                date('Y-m-d', strtotime($filters['jatuh_tempo_awal'])),
                date('Y-m-d', strtotime($filters['jatuh_tempo_akhir'])),
            ]);
        }

        if (! empty($filters['nama_toko'])) {
            $query->whereIn('toko_id', (array) $filters['nama_toko']);
        }

        if (! empty($filters['nama_supplier'])) {
            $query->whereIn('supplier_id', (array) $filters['nama_supplier']);
        }

        if ($user && (int) $user->status !== 1) {
            $query->where('toko_id', $user->toko_id);
        }

        if (isset($filters['status_terima']) && $filters['status_terima'] !== '' && (int) $filters['status_terima'] !== 2) {
            $query->where('status_terima', (int) $filters['status_terima']);
        }

        if (isset($filters['status_bayar']) && $filters['status_bayar'] !== '' && (int) $filters['status_bayar'] !== 3) {
            if ((int) $filters['status_bayar'] === 0 || (int) $filters['status_bayar'] === 1) {
                $query->where('status_bayar', (int) $filters['status_bayar'])->where('po_dp', 0);
            } else {
                $query->where('po_dp', '>', 0);
            }
        }

        $rows = [];
        $total = 0;

        foreach ($query->orderBy('date', 'desc')->get() as $po) {
            $subTotal = (float) $po->subtotal;
            if ((float) $po->ppn != 0) {
                $subTotal += $subTotal * 11 / 100;
            }
            $total += $subTotal;

            $statusBayar = (int) $po->status_bayar === 1 ? 'Lunas' : ((float) $po->po_dp > 0 ? 'DP' : 'Hutang');

            $rows[] = [
                $po->id,
                $po->date ? date('d-F-Y', strtotime($po->date)) : '',
                $po->kode_po,
                $po->supplier?->nama_supplier ?? $po->nama_supplier,
                number_format((float) $po->subtotal),
                (int) $po->status === 2 ? 'Draft' : 'Dikirim',
                (int) $po->status_terima === 1 ? 'Sudah DiTerima' : 'Belum DiTerima',
                $statusBayar,
                $po->jatuh_tempo ? date('d-F-Y', strtotime($po->jatuh_tempo)) : '',
            ];
        }

        $rows[] = ['', '', '', 'TOTAL', number_format($total)];

        return [
            'headings' => ['Id', 'Tanggal', 'Kode PO', 'Nama Supplier', 'Total Pembayaran', 'Status PO', 'Status Barang', 'Status Bayar', 'Jatuh Tempo'],
            'rows' => $rows,
        ];
    }

    /**
     * @return BinaryFileResponse
     */
    public function export(string $type, Request $request)
    {
        $filters = $request->query();
        $report = match ($type) {
            'barang-masuk' => $this->barangMasuk($filters),
            'penjualan' => $this->penjualan($filters),
            'barang-keluar' => $this->barangKeluar($filters),
            'pindah-barang' => $this->pindahBarang($filters),
            'stock' => $this->stock($filters),
            'laba-rugi' => $this->labaRugi($filters),
            'po' => $this->po($filters),
            default => abort(404),
        };

        $filename = match ($type) {
            'barang-masuk' => 'Report Barang Masuk',
            'penjualan' => 'Report Penjualan',
            'barang-keluar' => 'Report Barang Keluar',
            'pindah-barang' => 'Report Pindah Barang',
            'stock' => 'Report Stock',
            'laba-rugi' => 'Report Laba Rugi',
            'po' => 'Report Purchase Order',
        };

        $data = array_merge([$report['headings']], $report['rows']);

        return Excel::download(new ReportExport($data), $filename.'.xlsx');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{0: string, 1: string}
     */
    private function range(array $filters): array
    {
        $from = ! empty($filters['tanggalAwal'])
            ? Carbon::parse($filters['tanggalAwal'])->startOfDay()
            : now()->startOfMonth();

        $to = ! empty($filters['tanggalAkhir'])
            ? Carbon::parse($filters['tanggalAkhir'])->endOfDay()
            : now()->endOfMonth();

        return [$from->toDateTimeString(), $to->toDateTimeString()];
    }
}
