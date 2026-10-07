<?php

namespace App\Filament\Resources\WhitelistedIps;

use App\Filament\Resources\WhitelistedIps\Pages\CreateWhitelistedIp;
use App\Filament\Resources\WhitelistedIps\Pages\EditWhitelistedIp;
use App\Filament\Resources\WhitelistedIps\Pages\ListWhitelistedIps;
use App\Filament\Resources\WhitelistedIps\Schemas\WhitelistedIpForm;
use App\Filament\Resources\WhitelistedIps\Tables\WhitelistedIpsTable;
use App\Models\WhitelistedIp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WhitelistedIpResource extends Resource
{
    protected static ?string $model = WhitelistedIp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Whitelist IP';

    protected static ?string $modelLabel = 'Whitelist IP';

    protected static ?string $pluralModelLabel = 'Whitelist IP';

    protected static ?string $recordTitleAttribute = 'ip_address';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return WhitelistedIpForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhitelistedIpsTable::configure($table);
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
            'index' => ListWhitelistedIps::route('/'),
            'create' => CreateWhitelistedIp::route('/create'),
            'edit' => EditWhitelistedIp::route('/{record}/edit'),
        ];
    }
}
