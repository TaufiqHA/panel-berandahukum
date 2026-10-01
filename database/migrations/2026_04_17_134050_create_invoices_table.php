<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('kode_invoice')->nullable();
            $table->string('nama_pembeli')->nullable();
            $table->text('alamat_pembeli')->nullable();
            $table->string('tlp_pembeli')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->foreignId('toko_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount_value')->nullable();
            $table->string('waktu')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('total_pembayaran')->nullable();
            $table->string('dp_payment')->nullable();
            $table->string('nama_sales')->nullable();
            $table->string('ppn')->nullable();
            $table->string('sisa')->nullable();
            $table->string('status')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('show_infopembayaran')->nullable();
            $table->string('show_option')->nullable();
            $table->string('option_text')->nullable();
            $table->string('show_project')->nullable();
            $table->string('nama_project')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
