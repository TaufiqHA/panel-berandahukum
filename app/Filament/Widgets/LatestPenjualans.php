<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPenjualans extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $tokoId = ($user && (int) $user->status === 2) ? $user->toko_id : null;

        return $table
            ->heading('Penjualan Terbaru')
            ->query(
                fn (): Builder => Penjualan::query()
                    ->with('toko')
                    ->when($tokoId, fn (Builder $q) => $q->where('toko_id', $tokoId))
                    ->latest('date')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('date')->limit(30)
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('kode_penjualan')->limit(30)
                    ->label('Kode'),
                TextColumn::make('nama_pembeli')->limit(30)
                    ->label('Pembeli'),
                TextColumn::make('toko.nama_toko')->limit(30)
                    ->label('Toko'),
                TextColumn::make('subtotal')->limit(30)
                    ->label('Subtotal')
                    ->money('IDR', divideBy: 1),
                TextColumn::make('status')->limit(30)
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 2 ? 'Draft' : 'Done')
                    ->color(fn ($state): string => (int) $state === 2 ? 'gray' : 'success'),
            ])
            ->paginated(false);
    }
}
