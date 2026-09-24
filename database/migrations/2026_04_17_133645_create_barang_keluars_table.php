<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('kode_barang_keluar')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->string('telepon_penerima')->nullable();
            $table->foreignId('toko_id')->nullable();
            $table->string('waktu')->nullable();
            $table->string('nama_sales')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('barang_keluars');
    }
}
