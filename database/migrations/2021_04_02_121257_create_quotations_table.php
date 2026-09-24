<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('kode_quotation');
            $table->string('nama_pembeli');
            $table->text('alamat_pembeli')->nullable();
            $table->string('telepon')->nullable();
            $table->foreignId('toko_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->double('subtotal')->nullable();
            $table->integer('ppn')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
