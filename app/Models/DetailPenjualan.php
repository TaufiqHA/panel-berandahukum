<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'gudang_barang_id',
        'serial_number_id',
        'discount',
        'price',
    ];

    public function serial_number()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang_barang()
    {
        return $this->belongsTo(GudangBarang::class)->withTrashed();
    }

    /**
     * Resolve the serial number text for this sale line.
     *
     * The stock unit owns the real serial number text. On some imported rows
     * `detail_penjualans.serial_number_id` wrongly stores the unit id, so the
     * unit is checked first and the detail column is only used as a fallback.
     */
    public function resolveSerialNumber(): string
    {
        $unit = $this->gudang_barang;

        if ($unit !== null) {
            $serial = self::serialNumberValue($unit->serial_number_id)
                ?? self::serialNumberValue(optional($unit->serial_number)->serial_number);

            if (! empty($serial)) {
                return $serial;
            }
        }

        return self::serialNumberValue($this->serial_number_id) ?? '';
    }

    private static function serialNumberValue(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $serialNumber = SerialNumber::withTrashed()->find($value);

        if ($serialNumber !== null && ! empty($serialNumber->serial_number)) {
            return $serialNumber->serial_number;
        }

        return is_scalar($value) ? (string) $value : null;
    }
}
