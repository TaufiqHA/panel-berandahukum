<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('kode_penjualan');
            $table->string('nama_pembeli');
            $table->text('alamat_pembeli')->nullable();
            $table->string('telepon')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->foreignId('toko_id');
            $table->string('payment_status')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount_value')->nullable();
            $table->integer('waktu')->nullable();
            $table->double('subtotal')->nullable();
            $table->double('total_pembayaran')->nullable();
            $table->double('dp_payment')->nullable();
            $table->double('sisa')->nullable();
            $table->foreignId('user_id');
            $table->integer('ppn');
            $table->tinyInteger('status');            
            $table->text('keterangan')->nullable();
            $table->tinyInteger('show_infopembayaran');
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
        Schema::dropIfExists('penjualans');
    }
}
