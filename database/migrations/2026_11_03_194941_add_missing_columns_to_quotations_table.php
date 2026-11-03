<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'nama_sales')) {
                $table->string('nama_sales')->nullable()->after('toko_id');
            }

            if (! Schema::hasColumn('quotations', 'show_option')) {
                $table->tinyInteger('show_option')->default(0)->after('show_infopembayaran');
            }

            if (! Schema::hasColumn('quotations', 'option_text')) {
                $table->text('option_text')->nullable()->after('show_option');
            }

            if (! Schema::hasColumn('quotations', 'show_project')) {
                $table->tinyInteger('show_project')->default(0)->after('option_text');
            }

            if (! Schema::hasColumn('quotations', 'nama_project')) {
                $table->text('nama_project')->nullable()->after('show_project');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'nama_sales',
                'show_option',
                'option_text',
                'show_project',
                'nama_project',
            ]);
        });
    }
};
