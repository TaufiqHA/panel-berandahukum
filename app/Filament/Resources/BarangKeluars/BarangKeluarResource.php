<?php

namespace App\Filament\Resources\BarangKeluars;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\BarangKeluars\Pages\CreateBarangKeluar;
use App\Filament\Resources\BarangKeluars\Pages\EditBarangKeluar;
use App\Filament\Resources\BarangKeluars\Pages\ListBarangKeluars;
use App\Filament\Resources\BarangKeluars\Schemas\BarangKeluarForm;
use App\Filament\Resources\BarangKeluars\Tables\BarangKeluarsTable;
use App\Models\BarangKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BarangKeluarResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Barang Keluar';
    }

    protected static ?string $model = BarangKeluar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Barang';

    protected static ?string $navigationLabel = 'Barang Keluar';

    protected static ?string $modelLabel = 'Barang Keluar';

    protected static ?string $pluralModelLabel = 'Barang Keluar';

    protected static ?string $recordTitleAttribute = 'kode_barang_keluar';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BarangKeluarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangKeluarsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && (int) $user->status === 2) {
            $query->where('toko_id', $user->toko_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarangKeluars::route('/'),
            'create' => CreateBarangKeluar::route('/create'),
            'edit' => EditBarangKeluar::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
