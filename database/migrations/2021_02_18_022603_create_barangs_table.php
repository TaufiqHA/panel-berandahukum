<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable();
            $table->string('nama_product');
            $table->string('merk')->nullable();
            $table->string('satuan')->nullable();
            $table->string('warna')->nullable();
            $table->string('berat')->nullable();
            $table->string('ukuran')->nullable();
            $table->longText('keterangan')->nullable();
            $table->boolean('wajib_serial_number')->nullable();
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
        Schema::dropIfExists('barangs');
    }
}
