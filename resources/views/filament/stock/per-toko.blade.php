@php
    $rows = \App\Models\GudangBarang::query()
        ->where('barang_id', $record->id)
        ->selectRaw('toko_id, count(*) as total')
        ->groupBy('toko_id')
        ->with('toko')
        ->get();
@endphp

<table style="width:100%;border-collapse:collapse;font-size:14px;">
    <thead>
        <tr style="text-align:left;border-bottom:1px solid rgba(128,128,128,.3);">
            <th style="padding:8px 4px;">Toko</th>
            <th style="padding:8px 4px;text-align:right;">Stock</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $row)
            <tr style="border-bottom:1px solid rgba(128,128,128,.15);">
                <td style="padding:8px 4px;">{{ $row->toko?->nama_toko ?? '-' }}</td>
                <td style="padding:8px 4px;text-align:right;">{{ number_format($row->total, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="padding:8px 4px;">Belum ada stock.</td>
            </tr>
        @endforelse
    </tbody>
</table>
