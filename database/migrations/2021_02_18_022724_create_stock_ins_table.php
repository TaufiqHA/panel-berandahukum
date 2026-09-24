<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id');
            $table->double('jumlah')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->double('harga_beli')->nullable();
            $table->double('harga_jual')->nullable();
            $table->double('price_list')->nullable();
            $table->string('made_in')->nullable();
            $table->string('supplier')->nullable();
            $table->foreignId('toko_id');
            $table->tinyInteger('type_serial_number')->nullable();
            $table->longText('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_ins');
    }
}
