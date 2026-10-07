<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\DataBarangKeluar;
use App\Models\DetailBarangKeluar;
use App\Models\DetailPenjualan;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\PindahGudang;
use App\Models\Po;
use App\Models\Quotation;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function penjualanDownload($id)
    {
        $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'barang_pembelian'])->where('id', $id)->first();
        $this->attachSerialNumbers($penjualan);

        $setting = Setting::where('toko_id', $penjualan->toko_id)->first();
        $penjualan['cara_pembayaran'] = $setting ? $setting->cara_pembayaran : '';

        $pdf = Pdf::loadView('penjualan.print', $penjualan->toArray());

        return $pdf->stream();
    }

    public function penjualanSuratJalan($id)
    {
        $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'barang_pembelian'])->where('id', $id)->first();
        $this->attachSerialNumbers($penjualan);

        $pdf = Pdf::loadView('penjualan.surat-jalan', $penjualan->toArray());

        return $pdf->stream();
    }

    public function invoicePrint($id)
    {
        $invoice = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();

        $setting = Setting::where('toko_id', $invoice->toko_id)->first();
        $invoice['cara_pembayaran'] = $setting ? $setting->cara_pembayaran : '';

        $pdf = Pdf::loadView('invoice.print', $invoice->toArray());

        return $pdf->stream();
    }

    public function quotationPrint($id)
    {
        $quotation = Quotation::with(['toko', 'detail_quotation.barang', 'barang_pembelian', 'user'])->where('id', $id)->first();

        $setting = Setting::where('toko_id', $quotation->toko_id)->first();
        $quotation['cara_pembayaran'] = $setting ? $setting->cara_pembayaran : '';

        $pdf = Pdf::loadView('quotation.print', $quotation->toArray());

        return $pdf->stream();
    }

    public function poPrint($id)
    {
        $po = Po::with(['toko', 'detail_po.barang', 'barang_pembelian', 'user'])->where('id', $id)->first();

        $pdf = Pdf::loadView('po.print', $po->toArray());

        return $pdf->stream();
    }

    public function barangKeluarPrint($id)
    {
        $penjualan = BarangKeluar::with(['toko', 'barang_pembelian'])->where('id', $id)->first();

        $this->attachBarangKeluarSerialNumbers($penjualan);

        $pdf = Pdf::loadView('barang-keluar.print', $penjualan->toArray());

        return $pdf->stream();
    }

    public function barangKeluarSuratJalan($id)
    {
        $penjualan = BarangKeluar::with(['toko', 'data_barang_keluar.barang', 'barang_pembelian'])->where('id', $id)->first();

        $this->attachBarangKeluarSerialNumbers($penjualan);

        $pdf = Pdf::loadView('barang-keluar.surat-jalan', $penjualan->toArray());

        return $pdf->stream();
    }

    private function attachBarangKeluarSerialNumbers(BarangKeluar $barangKeluar): void
    {
        foreach ($barangKeluar->barang_pembelian as $barang) {
            $detail_barang_keluar = DataBarangKeluar::with('gudang_barang.serial_number')
                ->where('barang_id', $barang->pivot->barang_id)
                ->where('barang_keluar_id', $barang->pivot->barang_keluar_id)
                ->get();

            $serial_numbers = [];
            foreach ($detail_barang_keluar as $detail) {
                $serial_number = optional(optional($detail->gudang_barang)->serial_number)->serial_number;
                if (! empty($serial_number) && ! in_array($serial_number, $serial_numbers)) {
                    $serial_numbers[] = $serial_number;
                }
            }

            $barang->pivot['serial_number'] = implode(', ', $serial_numbers);
        }
    }

    public function pindahTokoOutDownload($id)
    {
        return $this->pindahTokoDownload($id);
    }

    public function pindahTokoInDownload($id)
    {
        return $this->pindahTokoDownload($id);
    }

    private function pindahTokoDownload($id)
    {
        $pindahGudang = PindahGudang::with(['toko', 'toko_to', 'data_detail_barang', 'data_detail_serial_number', 'detail_barang_keluar', 'barang_pindah'])->where('id', $id)->first();

        for ($i = 0; $i < count($pindahGudang->barang_pindah); $i++) {
            $detail_barang_keluar = DetailBarangKeluar::with(['gudang_barang.serial_number', 'serial_number'])
                ->where('barang_id', $pindahGudang->barang_pindah[$i]->pivot->barang_id)
                ->where('pindah_gudang_id', $pindahGudang->barang_pindah[$i]->pivot->pindah_gudang_id)
                ->get();

            $data_sn[$i] = [];
            foreach ($detail_barang_keluar as $detail) {
                $serial_number = optional($detail->serial_number)->serial_number;
                if (empty($serial_number)) {
                    $serial_number = optional(optional($detail->gudang_barang)->serial_number)->serial_number;
                }
                if (! empty($serial_number)) {
                    $data_sn[$i][] = $serial_number;
                }
            }
            $pindahGudang->barang_pindah[$i]->pivot['serial_number'] = implode(', ', $data_sn[$i]);
        }

        $pindahGudang['nama_pengirim'] = auth()->user()->name;

        $pdf = Pdf::loadView('pindah-toko.barang-keluar.print', $pindahGudang->toArray());

        return $pdf->stream();
    }

    private function resolveSerialNumber($detail)
    {
        return $detail->resolveSerialNumber();
    }

    private function attachSerialNumbers($penjualan)
    {
        foreach ($penjualan->barang_pembelian as $barang) {
            $detail_penjualan = DetailPenjualan::with(['gudang_barang.serial_number', 'serial_number'])
                ->where('barang_id', $barang->pivot->barang_id)
                ->where('penjualan_id', $barang->pivot->penjualan_id)
                ->get();

            $data_sn = [];
            foreach ($detail_penjualan as $detail) {
                $serial_number = $this->resolveSerialNumber($detail);
                if (! empty($serial_number)) {
                    $data_sn[] = $serial_number;
                }
            }

            $barang->pivot['serial_number'] = implode(', ', $data_sn);
        }

        return $penjualan;
    }
}
