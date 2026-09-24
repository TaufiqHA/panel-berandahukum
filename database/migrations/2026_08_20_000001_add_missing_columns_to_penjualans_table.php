<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'nama_sales')) {
                $table->string('nama_sales')->nullable()->after('sisa');
            }
            if (!Schema::hasColumn('penjualans', 'show_option')) {
                $table->string('show_option')->nullable()->default('0')->after('show_infopembayaran');
            }
            if (!Schema::hasColumn('penjualans', 'option_text')) {
                $table->string('option_text')->nullable()->after('show_option');
            }
            if (!Schema::hasColumn('penjualans', 'show_project')) {
                $table->string('show_project')->nullable()->default('0')->after('option_text');
            }
            if (!Schema::hasColumn('penjualans', 'nama_project')) {
                $table->string('nama_project')->nullable()->after('show_project');
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
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_sales',
                'show_option',
                'option_text',
                'show_project',
                'nama_project'
            ]);
        });
    }
}
