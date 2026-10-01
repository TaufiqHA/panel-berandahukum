<?php

namespace App\Filament\Resources\PindahGudangs;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\PindahGudangs\Pages\CreatePindahGudang;
use App\Filament\Resources\PindahGudangs\Pages\EditPindahGudang;
use App\Filament\Resources\PindahGudangs\Pages\ListPindahGudangs;
use App\Filament\Resources\PindahGudangs\Schemas\PindahGudangForm;
use App\Filament\Resources\PindahGudangs\Tables\PindahGudangsTable;
use App\Models\DetailBarangKeluar;
use App\Models\GudangBarang;
use App\Models\PindahGudang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PindahGudangResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Pindah Toko';
    }

    protected static ?string $model = PindahGudang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Pindah Toko';

    protected static ?string $navigationLabel = 'Barang Keluar';

    protected static ?string $modelLabel = 'Barang Keluar';

    protected static ?string $pluralModelLabel = 'Barang Keluar';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PindahGudangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PindahGudangsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user && (int) $user->status === 2) {
            $query->where('from', $user->toko_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPindahGudangs::route('/'),
            'create' => CreatePindahGudang::route('/create'),
            'edit' => EditPindahGudang::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Mark a transfer as received and move the units to the destination toko.
     */
    public static function receive(int $id): void
    {
        $pindahGudang = PindahGudang::find($id);

        if (! $pindahGudang) {
            return;
        }

        PindahGudang::where('id', $id)->update(['status' => 2]);
        DetailBarangKeluar::where('pindah_gudang_id', $id)->update(['status' => 2]);

        foreach (DetailBarangKeluar::where('pindah_gudang_id', $id)->get() as $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->update([
                'toko_id' => $pindahGudang->to,
                'status' => 1,
            ]);
        }
    }

    /**
     * Revert the moved units back to the origin toko and remove the transfer.
     */
    public static function revertAndDelete(int $id): void
    {
        foreach (DetailBarangKeluar::where('pindah_gudang_id', $id)->get() as $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->update(['status' => 1]);
        }

        DetailBarangKeluar::where('pindah_gudang_id', $id)->delete();
        PindahGudang::destroy($id);
    }
}
