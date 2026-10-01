<?php

namespace App\Filament\Resources\Tokos;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\Tokos\Pages\CreateToko;
use App\Filament\Resources\Tokos\Pages\EditToko;
use App\Filament\Resources\Tokos\Pages\ListTokos;
use App\Filament\Resources\Tokos\Schemas\TokoForm;
use App\Filament\Resources\Tokos\Tables\TokosTable;
use App\Models\Toko;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TokoResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Toko';
    }

    protected static ?string $model = Toko::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Toko';

    protected static ?string $modelLabel = 'Toko';

    protected static ?string $pluralModelLabel = 'Toko';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return TokoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TokosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTokos::route('/'),
            'create' => CreateToko::route('/create'),
            'edit' => EditToko::route('/{record}/edit'),
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
