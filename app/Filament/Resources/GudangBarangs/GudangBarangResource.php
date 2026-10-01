<?php

namespace App\Filament\Resources\GudangBarangs;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\GudangBarangs\Pages\CreateGudangBarang;
use App\Filament\Resources\GudangBarangs\Pages\EditGudangBarang;
use App\Filament\Resources\GudangBarangs\Pages\ListGudangBarangs;
use App\Filament\Resources\GudangBarangs\Schemas\GudangBarangForm;
use App\Filament\Resources\GudangBarangs\Tables\GudangBarangsTable;
use App\Models\GudangBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GudangBarangResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Stock';
    }

    protected static ?string $model = GudangBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Stock Detail';

    protected static ?string $modelLabel = 'Stock';

    protected static ?string $pluralModelLabel = 'Stock';

    public static function form(Schema $schema): Schema
    {
        return GudangBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GudangBarangsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGudangBarangs::route('/'),
            'create' => CreateGudangBarang::route('/create'),
            'edit' => EditGudangBarang::route('/{record}/edit'),
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
