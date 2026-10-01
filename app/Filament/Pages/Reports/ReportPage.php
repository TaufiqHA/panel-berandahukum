<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Http\Controllers\ReportController;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Toko;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

abstract class ReportPage extends Page implements HasTable
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Report';
    }

    protected static function blockedForAdminPusat(): bool
    {
        return true;
    }

    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Report';

    protected string $view = 'filament.report.index';

    protected static string $reportType = '';

    /**
     * @var array<int, array{key: string, label: string, type: string, source?: string, choices?: array<string, string>}>
     */
    protected static array $filterFields = [];

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $defaults = [];

        foreach (static::$filterFields as $field) {
            $defaults[$field['key']] = $field['type'] === 'multiselect' ? [] : null;
        }

        $this->form->fill($defaults);
    }

    public function form(Schema $schema): Schema
    {
        $fields = [];

        foreach (static::$filterFields as $field) {
            $component = match ($field['type']) {
                'date' => DatePicker::make($field['key'])->label($field['label'])->native(false),
                'multiselect' => Select::make($field['key'])->label($field['label'])->options($this->optionsFor($field['source'] ?? null))->multiple()->searchable()->preload(),
                default => Select::make($field['key'])->label($field['label'])->options($field['choices'] ?? [])->searchable(),
            };

            $fields[] = $component->live();
        }

        return $schema->columns(3)->components($fields)->statePath('data');
    }

    public function table(Table $table): Table
    {
        $report = $this->report();

        $columns = [];

        foreach ($report['headings'] as $index => $heading) {
            $columns[] = TextColumn::make((string) $index)
                ->label($heading)
                ->state(fn ($record): mixed => $record[$index] ?? '')
                ->wrap();
        }

        return $table
            ->columns($columns)
            ->records(fn (): array => $this->report()['rows'])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->striped()
            ->emptyStateHeading('Data Not Available');
    }

    /**
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    protected function report(): array
    {
        $filters = $this->data ?? [];

        return match (static::$reportType) {
            'barang-masuk' => app(ReportController::class)->barangMasuk($filters),
            'penjualan' => app(ReportController::class)->penjualan($filters),
            'pindah-barang' => app(ReportController::class)->pindahBarang($filters),
            'stock' => app(ReportController::class)->stock($filters),
            'laba-rugi' => app(ReportController::class)->labaRugi($filters),
            'po' => app(ReportController::class)->po($filters),
            default => ['headings' => [], 'rows' => []],
        };
    }

    /**
     * @return array<int, string>|array<int|string, string>
     */
    protected function optionsFor(?string $source): array
    {
        return match ($source) {
            'barang' => Barang::query()->orderBy('nama_product')->pluck('nama_product', 'id')->all(),
            'toko' => Toko::query()->orderBy('nama_toko')->pluck('nama_toko', 'id')->all(),
            'supplier' => Supplier::query()->orderBy('nama_supplier')->pluck('nama_supplier', 'id')->all(),
            default => [],
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter')
                ->color('warning')
                ->action(fn () => null),
            Action::make('download')
                ->label('Download')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('report.export', array_merge(['type' => static::$reportType], $this->data ?? []))),
        ];
    }
}
