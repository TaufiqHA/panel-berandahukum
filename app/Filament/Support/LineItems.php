<?php

namespace App\Filament\Support;

use App\Models\Barang;
use App\Models\GudangBarang;
use App\Models\Signature;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class LineItems
{
    /**
     * Discount is stored as a percentage, matching the legacy behaviour.
     *
     * @param  array<string, mixed>  $item
     */
    public static function lineSubtotal(array $item): float
    {
        $price = self::toNumber($item['price'] ?? 0);
        $quantity = self::toNumber($item['jumlah'] ?? 1);
        $discount = self::toNumber($item['discount'] ?? 0);

        return round($price * $quantity * (1 - $discount / 100));
    }

    /**
     * Normalise a value that may be a number or an Indonesian formatted string
     * (e.g. "1.385.000") into a float.
     */
    public static function toNumber(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) preg_replace('/[^0-9-]/', '', (string) $value);
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $items
     */
    public static function sum(?array $items): float
    {
        return (float) Collection::make($items ?? [])->sum(fn (array $item): float => self::lineSubtotal($item));
    }

    /**
     * @return array<int, string>
     */
    public static function barangOptions(): array
    {
        return Barang::query()
            ->orderBy('nama_product')
            ->pluck('nama_product', 'id')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function salesOptions(): array
    {
        return Signature::query()
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    public static function rupiah(float|int|string|null $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }

    /**
     * Human readable label for a stock unit, e.g. "Speaker JBL — SN-001".
     */
    public static function unitLabel(GudangBarang $unit): string
    {
        $name = $unit->barang?->nama_product ?? 'Barang #'.$unit->barang_id;

        return trim($name.' — '.($unit->serial_number_id ?: 'tanpa SN'));
    }

    /**
     * Human readable serial number only, e.g. "SN-001".
     */
    public static function unitSerialLabel(GudangBarang $unit): string
    {
        return $unit->serial_number_id ?: 'tanpa SN';
    }

    /**
     * @return array<int, string>
     */
    public static function unitOptions(?int $barangId, ?int $tokoId): array
    {
        if (! $barangId || ! $tokoId) {
            return [];
        }

        return GudangBarang::query()
            ->where('barang_id', $barangId)
            ->where('toko_id', $tokoId)
            ->where('status', 1)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (GudangBarang $unit): array => [$unit->id => self::unitSerialLabel($unit)])
            ->all();
    }

    public static function stockCount(mixed $barangId, mixed $tokoId): int
    {
        if (! $barangId || ! $tokoId) {
            return 0;
        }

        return GudangBarang::query()
            ->where('barang_id', $barangId)
            ->where('toko_id', $tokoId)
            ->where('status', 1)
            ->count();
    }

    /**
     * Repeater for barang-only line items (Purchase Order, Quotation, Invoice).
     */
    public static function barangRepeater(bool $withStock = false): Repeater
    {
        $schema = [
            Select::make('barang_id')
                ->label('Pilih Barang')
                ->options(fn (): array => self::barangOptions())
                ->wrapOptionLabels(false)->searchable()
                ->live()
                ->getOptionLabelUsing(fn ($value): ?string => Barang::withTrashed()->find($value)?->nama_product)
                ->required(),
        ];

        if ($withStock) {
            $schema[] = Placeholder::make('stock')
                ->label('Stock')
                ->content(fn ($get): HtmlString => new HtmlString(self::rupiah(self::stockCount($get('barang_id'), $get('../../toko_id')))));
        }

        $schema[] = TextInput::make('jumlah')
            ->label('Jumlah')
            ->numeric()
            ->default(1)
            ->minValue(1)
            ->live()
            ->required();

        $schema[] = TextInput::make('price')
            ->label('Harga')
            ->money()
            ->live()
            ->required();

        $schema[] = TextInput::make('discount')
            ->label('Discount %')
            ->numeric()
            ->live();

        $schema[] = Placeholder::make('subtotal')
            ->label('Jumlah')
            ->content(fn ($get): HtmlString => new HtmlString(self::rupiah(self::lineSubtotal([
                'price' => $get('price'),
                'jumlah' => $get('jumlah'),
                'discount' => $get('discount'),
            ]))));

        return Repeater::make('items')
            ->hiddenLabel()
            ->addActionLabel('Barang')
            ->schema($schema)
            ->columns($withStock ? 6 : 5)
            ->columnSpanFull();
    }

    /**
     * Repeater for stock-unit line items (Penjualan, Barang Keluar), where each
     * row lets you pick a barang, a quantity and the concrete serial numbers.
     */
    public static function serialRepeater(bool $withPrice = false): Repeater
    {
        $schema = [
            Select::make('barang_id')
                ->label('Pilih Barang')
                ->options(fn (): array => self::barangOptions())
                ->wrapOptionLabels(false)->searchable()
                ->live()
                ->getOptionLabelUsing(fn ($value): ?string => Barang::withTrashed()->find($value)?->nama_product)
                ->columnSpan($withPrice ? 3 : 1)
                ->required(),
            Placeholder::make('stock')
                ->label('Stock')
                ->content(fn ($get): HtmlString => new HtmlString(self::rupiah(self::stockCount($get('barang_id'), $get('../../toko_id'))))),
            TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->live()
                ->required(),
        ];

        if ($withPrice) {
            $schema[] = Select::make('serial_numbers')
                ->label('Pilih Serial Number')
                ->multiple()
                ->columnSpan(2)
                ->options(fn ($get): array => self::unitOptions($get('barang_id'), $get('../../toko_id')))
                ->wrapOptionLabels(false)->searchable()
                ->getOptionLabelsUsing(fn (array $values): array => GudangBarang::withTrashed()
                    ->whereIn('id', $values)
                    ->get()
                    ->mapWithKeys(fn (GudangBarang $unit): array => [(string) $unit->id => self::unitSerialLabel($unit)])
                    ->all());

            $schema[] = TextInput::make('price')
                ->label('Harga')
                ->money()
                ->live()
                ->required();

            $schema[] = TextInput::make('discount')
                ->label('Discount %')
                ->numeric()
                ->live();

            $schema[] = Placeholder::make('subtotal')
                ->label('Jumlah')
                ->content(fn ($get): HtmlString => new HtmlString(self::rupiah(self::lineSubtotal([
                    'price' => $get('price'),
                    'jumlah' => $get('jumlah'),
                    'discount' => $get('discount'),
                ]))));
        } else {
            $schema[] = Select::make('serial_numbers')
                ->label('Pilih Serial Number')
                ->multiple()
                ->options(fn ($get): array => self::unitOptions($get('barang_id'), $get('../../toko_id')))
                ->wrapOptionLabels(false)->searchable()
                ->getOptionLabelsUsing(fn (array $values): array => GudangBarang::withTrashed()
                    ->whereIn('id', $values)
                    ->get()
                    ->mapWithKeys(fn (GudangBarang $unit): array => [(string) $unit->id => self::unitSerialLabel($unit)])
                    ->all());
        }

        return Repeater::make('items')
            ->hiddenLabel()
            ->addActionLabel('Barang')
            ->schema($schema)
            ->columns($withPrice ? 10 : 4)
            ->columnSpanFull();
    }
}
