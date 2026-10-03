<?php

namespace App\Filament\Resources\PindahTokoIns;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\PindahTokoIns\Pages\ListPindahTokoIns;
use App\Filament\Resources\PindahTokoIns\Tables\PindahTokoInsTable;
use App\Models\PindahGudang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PindahTokoInResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Pindah Toko';
    }

    protected static ?string $model = PindahGudang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Pindah Toko';

    protected static ?string $navigationLabel = 'Barang Masuk Pindah';

    protected static ?string $modelLabel = 'Barang Masuk Pindah';

    protected static ?string $pluralModelLabel = 'Barang Masuk Pindah';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return PindahTokoInsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user && (int) $user->status === 2) {
            $query->where('to', $user->toko_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPindahTokoIns::route('/'),
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
