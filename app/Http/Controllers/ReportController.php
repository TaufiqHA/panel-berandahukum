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
use Illuminate\Database\Eloquent\Builder;
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
                $stock->id,
                $stock->barang->nama_product,
                $stock->jumlah,
                $stock->tanggal_masuk ? date('d F Y', strtotime($stock->tanggal_masuk)) : '',
                $this->rupiah((float) $stock->harga_beli),
                $this->rupiah((float) $stock->harga_jual),
                $this->rupiah((float) $stock->price_list),
                $stock->made_in,
                $stock->supplier,
                $stock->toko?->nama_toko,
                $stock->keterangan,
            ];
        }

        return [
            'headings' => ['No', 'ID Barang Masuk', 'Nama Barang', 'Jumlah', 'Tanggal Masuk', 'Harga Beli', 'Harga Jual', 'Price List', 'Made In', 'Supplier', 'Toko', 'Keterangan'],
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

        if (! empty($filters['status_bayar'])) {
            $query->where('payment_status', $filters['status_bayar']);
        }

        if ((string) ($filters['jenis_report'] ?? '1') === '2') {
            return $this->penjualanBerdasarkanPenjualan($query);
        }

        $rows = [];
        $no = 1;

        foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
            foreach ($penjualan->detail_penjualan as $detail) {
                $serial = $detail->resolveSerialNumber();

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
                    $this->rupiah((float) $detail->price - ((float) $detail->price * (float) $detail->discount / 100)),
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
     * @param  Builder<Penjualan>  $query
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function penjualanBerdasarkanPenjualan(Builder $query): array
    {
        $rows = [];
        $no = 1;
        $totalPembayaran = 0;

        foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
            $total = (float) $penjualan->subtotal;
            if ((float) $penjualan->ppn != 0) {
                $total += $total * 11 / 100;
            }

            $totalPembayaran += $total;

            $rows[] = [
                $no++,
                $penjualan->date ? date('d F Y', strtotime($penjualan->date)) : '',
                $penjualan->kode_penjualan,
                $penjualan->nama_pembeli,
                $penjualan->metode_pembayaran,
                $penjualan->toko?->nama_toko,
                $penjualan->payment_status,
                $this->rupiah($total),
                $this->rupiah($penjualan->dp_payment),
                $this->rupiah($penjualan->sisa),
                $penjualan->nama_project ?? '',
            ];
        }

        $rows[] = ['', 'TOTAL', '', '', '', '', '', $this->rupiah($totalPembayaran), '', '', ''];

        return [
            'headings' => ['No', 'Tanggal', 'Kode Penjualan', 'Nama Pembeli', 'Cara Bayar', 'Nama Toko', 'Status Bayar', 'Total Pembayaran', 'DP', 'Sisa', 'Nama Project'],
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
                    $this->rupiah($harga),
                    $barangKeluar->keterangan,
                ];
            }
        }

        return [
            'headings' => ['No', 'Kode Barang Keluar', 'Nama Barang', 'Serial Number', 'Nama Toko', 'Tanggal Keluar', 'Nama Penerima', 'Harga', 'Keterangan'],
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
                $value->gudang_barang?->detail_barang_masuk?->stock_in?->harga_beli !== null
                    ? $this->rupiah($value->gudang_barang->detail_barang_masuk->stock_in->harga_beli)
                    : '',
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

        $query = GudangBarang::with(['barang', 'detail_barang_masuk.stock_in', 'toko']);

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
                $unit->barang?->nama_product,
                $unit->serial_number_id ?? '',
                1,
                $unit->barang?->warna,
                $unit->toko?->nama_toko,
                $stockIn?->harga_beli !== null ? $this->rupiah($stockIn->harga_beli) : '',
                $stockIn?->price_list !== null ? $this->rupiah($stockIn->price_list) : '',
                $stockIn?->supplier ?? '',
            ];
        }

        return [
            'headings' => ['No', 'Tanggal Masuk', 'Nama Barang', 'Serial Number', 'Jumlah', 'Warna', 'Nama Toko', 'Harga Beli', 'Price List', 'Supplier'],
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

        // Hanya penjualan yang sudah lunas (tidak ada sisa pembayaran) dan bukan
        // DP yang dihitung sebagai laba/rugi.
        $query
            ->where('payment_status', '!=', 'DP')
            ->where(function (Builder $query): void {
                $query->whereNull('sisa')->orWhere('sisa', '<=', 0);
            });

        $rows = [];
        $no = 1;

        if ($jenis === '2') {
            $headings = ['No', 'Tanggal', 'Kode Penjualan', 'Nama Pembeli', 'Nama Toko', 'Status Bayar', 'Total Pembayaran', 'Total Hrg.Beli', 'Keuntungan', 'Nama Project'];
            $totalHrgBeli = $totalPembayaran = $totalUntung = 0;

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
                $totalUntung += $untung;

                $rows[] = [
                    $no++,
                    $penjualan->date ? date('d F Y', strtotime($penjualan->date)) : '',
                    $penjualan->kode_penjualan,
                    $penjualan->nama_pembeli,
                    $penjualan->toko?->nama_toko,
                    $penjualan->payment_status,
                    $this->rupiah($total),
                    $this->rupiah($hargaBeli),
                    $this->rupiah($untung),
                    $penjualan->nama_project ?? '',
                ];
            }

            $rows[] = ['', 'TOTAL', '', '', '', '', $this->rupiah($totalPembayaran), $this->rupiah($totalHrgBeli), $this->rupiah($totalUntung), ''];

            return ['headings' => $headings, 'rows' => $rows];
        }

        $headings = ['No', 'Tanggal', 'No Ref', 'Nama Barang', 'Serial Number', 'Nama Toko', 'Harga Beli', 'Harga Jual', 'Keuntungan'];
        $totalHrgBeli = $totalHrgJual = $totalUntung = 0;

        foreach ($query->orderBy('date', 'desc')->get() as $penjualan) {
            foreach ($penjualan->detail_penjualan as $detail) {
                $hargaBeli = (float) ($detail->gudang_barang?->detail_barang_masuk?->stock_in?->harga_beli ?? 0);
                $hargaJual = (float) $detail->price - ((float) $detail->price * (float) $detail->discount / 100);
                $untung = $hargaJual - $hargaBeli;

                $serial = $detail->resolveSerialNumber();

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
                    $this->rupiah($hargaBeli),
                    $this->rupiah($hargaJual),
                    $this->rupiah($untung),
                ];
            }
        }

        $rows[] = ['', 'TOTAL', '', '', '', '', $this->rupiah($totalHrgBeli), $this->rupiah($totalHrgJual), $this->rupiah($totalUntung)];

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
                $this->rupiah((float) $po->subtotal),
                (int) $po->status === 2 ? 'Draft' : 'Dikirim',
                (int) $po->status_terima === 1 ? 'Sudah DiTerima' : 'Belum DiTerima',
                $statusBayar,
                $po->jatuh_tempo ? date('d-F-Y', strtotime($po->jatuh_tempo)) : '',
            ];
        }

        $rows[] = ['', '', '', 'TOTAL', $this->rupiah($total)];

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

    private function rupiah(mixed $value): string
    {
        return number_format((float) $value, 0, ',', '.');
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
