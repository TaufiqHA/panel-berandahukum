@php
    $units = \App\Models\GudangBarang::query()
        ->with('toko')
        ->where('barang_id', $record->id)
        ->orderBy('toko_id')
        ->get();
@endphp

<div style="max-height:60vh;overflow:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead>
            <tr style="text-align:left;border-bottom:1px solid rgba(128,128,128,.3);">
                <th style="padding:8px 6px;">Id</th>
                <th style="padding:8px 6px;">Serial Number</th>
                <th style="padding:8px 6px;">Toko</th>
                <th style="padding:8px 6px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($units as $unit)
                <tr style="border-bottom:1px solid rgba(128,128,128,.15);">
                    <td style="padding:8px 6px;">{{ $unit->id }}</td>
                    <td style="padding:8px 6px;">{{ $unit->serial_number_id ?? '-' }}</td>
                    <td style="padding:8px 6px;">{{ $unit->toko?->nama_toko ?? '-' }}</td>
                    <td style="padding:8px 6px;">
                        <a href="{{ \App\Filament\Resources\GudangBarangs\GudangBarangResource::getUrl('edit', ['record' => $unit->id]) }}">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:12px 6px;text-align:center;">No data available in table</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
