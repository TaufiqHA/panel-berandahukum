<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use Filament\Widgets\ChartWidget;

class PenjualanChart extends ChartWidget
{
    protected ?string $heading = 'Penjualan 12 Bulan Terakhir';

    protected ?string $maxHeight = '200px';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = auth()->user();
        $tokoId = ($user && (int) $user->status === 2) ? $user->toko_id : null;

        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);

            $labels[] = $month->translatedFormat('M Y');

            $data[] = (float) Penjualan::query()
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->when($tokoId, fn ($q) => $q->where('toko_id', $tokoId))
                ->sum('subtotal');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointBorderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
