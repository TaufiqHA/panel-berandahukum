<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BarangController extends Controller
{
    public function exportPdf(Request $request): StreamedResponse
    {
        // DomPDF membutuhkan memori besar saat merender daftar barang yang panjang.
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $query = Barang::query();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('nama_product', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        if ($kategoriId = $request->query('kategori_id')) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($namaProduct = $request->query('nama_product')) {
            $query->where('nama_product', $namaProduct);
        }

        if ($merk = $request->query('merk')) {
            $query->where('merk', $merk);
        }

        $records = $query->orderBy('id')->get();

        $pdf = Pdf::loadView('barang.pdf', [
            'records' => $records,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, 'Daftar Barang.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
