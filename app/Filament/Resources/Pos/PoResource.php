<?php

namespace App\Filament\Resources\Pos;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\Pos\Pages\CreatePo;
use App\Filament\Resources\Pos\Pages\EditPo;
use App\Filament\Resources\Pos\Pages\ListPos;
use App\Filament\Resources\Pos\Schemas\PoForm;
use App\Filament\Resources\Pos\Tables\PosTable;
use App\Models\Po;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PoResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Purchase Order';
    }

    protected static ?string $model = Po::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'Purchase Order';

    protected static ?string $modelLabel = 'Purchase Order';

    protected static ?string $pluralModelLabel = 'Purchase Order';

    protected static ?string $recordTitleAttribute = 'kode_po';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosTable::configure($table);
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
            'index' => ListPos::route('/'),
            'create' => CreatePo::route('/create'),
            'edit' => EditPo::route('/{record}/edit'),
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
