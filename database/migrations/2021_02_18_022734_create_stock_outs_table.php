<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->nullable();
            $table->date('date')->nullable();
            $table->string('nama_pembeli')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('payment_status')->nullable();//DP / Lunas / Termin
            $table->double('total_pembelian')->nullable();
            $table->double('jumlah_bayar')->nullable();
            $table->double('sisa')->nullable();
            $table->double('discount_type')->nullable();
            $table->double('discount')->nullable();
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
        Schema::dropIfExists('stock_outs');
    }
}
