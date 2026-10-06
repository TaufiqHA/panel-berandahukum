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
use Illuminate\Pagination\LengthAwarePaginator;
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
     * @var array{headings: array<int, string>, rows: array<int, array<int, mixed>>}|null
     */
    protected ?array $cachedReport = null;

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

    /**
     * The table columns depend on the selected filters (e.g. the laba rugi
     * report changes its headings based on "Jenis Laporan"). The table is built
     * during boot, before Livewire applies the filter update, so we rebuild it
     * here to keep the columns and records in sync.
     */
    public function updatedData(): void
    {
        $this->cachedReport = null;
        $this->table = $this->table($this->makeTable());
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
            ->records(function (int|string $page, int|string|null $recordsPerPage): LengthAwarePaginator {
                $rows = $this->report()['rows'];
                $total = count($rows);

                $perPage = ($recordsPerPage === 'all')
                    ? max($total, 1)
                    : (int) ($recordsPerPage ?: $this->getDefaultTableRecordsPerPageSelectOption());

                $page = max((int) $page, 1);

                return new LengthAwarePaginator(
                    array_slice($rows, ($page - 1) * $perPage, $perPage),
                    $total,
                    $perPage,
                    $page,
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'pageName' => $this->getTablePaginationPageName(),
                    ],
                );
            })
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
        if ($this->cachedReport !== null) {
            return $this->cachedReport;
        }

        $filters = $this->data ?? [];

        return $this->cachedReport = match (static::$reportType) {
            'barang-masuk' => app(ReportController::class)->barangMasuk($filters),
            'penjualan' => app(ReportController::class)->penjualan($filters),
            'barang-keluar' => app(ReportController::class)->barangKeluar($filters),
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
