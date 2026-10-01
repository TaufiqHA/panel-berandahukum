<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\GudangBarang;
use App\Models\Penjualan;
use App\Models\Po;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $tokoId = ($user && (int) $user->status === 2) ? $user->toko_id : null;

        $bulanIni = function ($query) {
            return $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        };

        $penjualanBulanIni = $bulanIni(
            Penjualan::query()->when($tokoId, fn ($q) => $q->where('toko_id', $tokoId))
        );

        $penjualanTotal = (clone $penjualanBulanIni)->sum('subtotal');

        $poBulanIni = $bulanIni(Po::query()->when($tokoId, fn ($q) => $q->where('toko_id', $tokoId)))->count();

        $stockTersedia = GudangBarang::query()
            ->where('status', 1)
            ->when($tokoId, fn ($q) => $q->where('toko_id', $tokoId))
            ->count();

        return [
            Stat::make('Total Barang', number_format(Barang::count(), 0, ',', '.'))
                ->description('Jenis barang')
                ->icon('heroicon-o-cube')
                ->color('primary'),
            Stat::make('Stock Tersedia', number_format($stockTersedia, 0, ',', '.'))
                ->description('Unit siap jual')
                ->icon('heroicon-o-circle-stack')
                ->color('success'),
            Stat::make('Penjualan Bulan Ini', (clone $penjualanBulanIni)->count())
                ->description('Rp '.number_format((float) $penjualanTotal, 0, ',', '.'))
                ->icon('heroicon-o-calculator')
                ->color('warning'),
            Stat::make('Purchase Order Bulan Ini', $poBulanIni)
                ->description('PO')
                ->icon('heroicon-o-document-text')
                ->color('info'),
        ];
    }
}
