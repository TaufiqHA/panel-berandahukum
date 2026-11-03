<?php

namespace App\Filament\Resources\Quotations;

use App\Filament\Concerns\ChecksMenuPermission;
use App\Filament\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Filament\Resources\Quotations\Schemas\QuotationForm;
use App\Filament\Resources\Quotations\Tables\QuotationsTable;
use App\Filament\Support\LineItems;
use App\Filament\Support\TransactionData;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class QuotationResource extends Resource
{
    use ChecksMenuPermission;

    protected static function menuPermission(): ?string
    {
        return 'Quotation';
    }

    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|UnitEnum|null $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'Quotation';

    protected static ?string $modelLabel = 'Quotation';

    protected static ?string $pluralModelLabel = 'Quotation';

    protected static ?string $recordTitleAttribute = 'kode_quotation';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
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
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function duplicate(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation): Quotation {
            $duplicate = $quotation->replicate([
                'kode_quotation',
                'date',
            ]);

            $duplicate->kode_quotation = Quotation::nextKodeQuotation();
            $duplicate->date = now()->toDateString();
            $duplicate->save();

            foreach ($quotation->detail_quotation()->get() as $detail) {
                $duplicate->detail_quotation()->create([
                    'barang_id' => $detail->barang_id,
                    'jumlah' => $detail->jumlah,
                    'discount' => $detail->discount,
                    'price' => $detail->price,
                    'subtotal' => $detail->subtotal,
                ]);
            }

            return $duplicate;
        });
    }

    public static function convertToInvoice(Quotation $quotation): Invoice
    {
        return DB::transaction(function () use ($quotation): Invoice {
            $lineItems = $quotation->detail_quotation()
                ->get()
                ->map(fn (QuotationDetail $detail): array => [
                    'barang_id' => $detail->barang_id,
                    'jumlah' => $detail->jumlah,
                    'discount' => $detail->discount,
                    'price' => $detail->price,
                ])
                ->all();

            $subtotal = LineItems::sum($lineItems);
            $usePpn = (float) $quotation->ppn > 0;
            $ppn = $usePpn ? round($subtotal * 0.11) : 0;
            $total = $subtotal + $ppn;

            $invoice = Invoice::create(TransactionData::withBuyerDefaults([
                'quotation_id' => $quotation->id,
                'date' => now()->toDateString(),
                'kode_invoice' => Invoice::nextKodeInvoice(),
                'nama_pembeli' => $quotation->nama_pembeli,
                'alamat_pembeli' => $quotation->alamat_pembeli,
                'tlp_pembeli' => $quotation->telepon,
                'metode_pembayaran' => 'Tunai',
                'toko_id' => $quotation->toko_id,
                'payment_status' => null,
                'waktu' => null,
                'subtotal' => $subtotal,
                'total_pembayaran' => $total,
                'dp_payment' => 0,
                'nama_sales' => $quotation->nama_sales,
                'ppn' => $ppn,
                'sisa' => 0,
                'status' => 1,
                'keterangan' => $quotation->keterangan,
                'show_infopembayaran' => $quotation->show_infopembayaran,
                'show_option' => $quotation->show_option,
                'option_text' => $quotation->option_text,
                'show_project' => $quotation->show_project,
                'nama_project' => $quotation->nama_project,
            ]));

            foreach ($lineItems as $item) {
                $invoice->detail_invoice()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'discount' => $item['discount'],
                    'price' => $item['price'],
                    'subtotal' => LineItems::lineSubtotal($item),
                ]);
            }

            return $invoice;
        });
    }
}
