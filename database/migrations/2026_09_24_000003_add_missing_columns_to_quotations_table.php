<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'nama_sales')) {
                $table->string('nama_sales')->nullable();
            }
            if (!Schema::hasColumn('quotations', 'show_option')) {
                $table->string('show_option')->nullable()->default('0');
            }
            if (!Schema::hasColumn('quotations', 'option_text')) {
                $table->string('option_text')->nullable();
            }
            if (!Schema::hasColumn('quotations', 'show_project')) {
                $table->string('show_project')->nullable()->default('0');
            }
            if (!Schema::hasColumn('quotations', 'nama_project')) {
                $table->string('nama_project')->nullable();
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
        Schema::table('quotations', function (Blueprint $table) {
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
