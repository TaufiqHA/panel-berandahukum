<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialNumberIdToDetailPenjualansTable extends Migration
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
                if (!Schema::hasColumn('detail_penjualans', 'serial_number_id')) {
                    $table->foreignId('serial_number_id')->nullable()->after('barang_id');
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
                if (Schema::hasColumn('detail_penjualans', 'serial_number_id')) {
                    $table->dropColumn('serial_number_id');
                }
            });
        }
    }
}
