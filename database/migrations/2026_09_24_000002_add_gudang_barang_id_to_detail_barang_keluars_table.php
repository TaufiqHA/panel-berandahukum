<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGudangBarangIdToDetailBarangKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_barang_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('gudang_barang_id')->nullable()->after('barang_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_barang_keluars', function (Blueprint $table) {
            $table->dropColumn('gudang_barang_id');
        });
    }
}
