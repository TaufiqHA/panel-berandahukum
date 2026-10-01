<?php

namespace App\Filament\Resources\Users;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'User';
    }

    protected static function blockedForAdminPusat(): bool
    {
        return true;
    }

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Translate the combined "status" form value into the legacy status
     * and status_admin columns.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyStatusMapping(array $data): array
    {
        if ((int) ($data['status'] ?? 0) === 3) {
            $data['status'] = 1;
            $data['status_admin'] = 1;
        } else {
            $data['status_admin'] = 0;
        }

        return $data;
    }

    /**
     * Translate the stored status / status_admin columns back into the
     * combined "status" value used by the form.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function resolveStatusForForm(array $data): array
    {
        if ((int) ($data['status'] ?? 0) === 1 && (int) ($data['status_admin'] ?? 0) === 1) {
            $data['status'] = 3;
        }

        return $data;
    }
}
