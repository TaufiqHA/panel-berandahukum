<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('kode_po')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->string('nama_purchase')->nullable();
            $table->string('nama_supplier')->nullable();
            $table->text('alamat_supplier')->nullable();
            $table->string('telepon')->nullable();
            $table->foreignId('toko_id')->nullable();
            $table->string('nama_sales')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('po_dp')->nullable();
            $table->string('ppn')->nullable();
            $table->string('status')->nullable();
            $table->string('status_terima')->nullable();
            $table->string('status_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('show_tempo')->nullable();
            $table->string('jatuh_tempo')->nullable();
            $table->text('alamat_kirim')->nullable();
            $table->string('jenis_brang')->nullable();
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
        Schema::dropIfExists('pos');
    }
}
