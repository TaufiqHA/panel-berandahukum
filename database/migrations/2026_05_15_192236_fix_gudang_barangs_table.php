<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixGudangBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gudang_barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('gudang_barangs', 'barang_id')) {
                $table->foreignId('barang_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('gudang_barangs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gudang_barangs', function (Blueprint $table) {
            $table->dropColumn(['barang_id', 'deleted_at']);
        });
    }
}
