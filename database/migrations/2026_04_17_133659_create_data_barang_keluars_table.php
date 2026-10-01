<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataBarangKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_keluar_id')->nullable();
            $table->foreignId('barang_id')->nullable();
            $table->foreignId('gudang_barang_id')->nullable();
            $table->string('discount')->nullable();
            $table->string('price')->nullable();
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
        Schema::dropIfExists('data_barang_keluars');
    }
}
