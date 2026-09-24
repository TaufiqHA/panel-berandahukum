<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGudangBarangIdToDetailPenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('detail_penjualans')) {
            Schema::table('detail_penjualans', function (Blueprint $table) {
                if (!Schema::hasColumn('detail_penjualans', 'gudang_barang_id')) {
                    $table->unsignedBigInteger('gudang_barang_id')->nullable()->after('penjualan_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('detail_penjualans')) {
            Schema::table('detail_penjualans', function (Blueprint $table) {
                if (Schema::hasColumn('detail_penjualans', 'gudang_barang_id')) {
                    $table->dropColumn('gudang_barang_id');
                }
            });
        }
    }
}
