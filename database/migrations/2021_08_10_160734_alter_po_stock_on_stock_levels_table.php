<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPoStockOnStockLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->renameColumn('post_stock', 'po_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->renameColumn('po_stock', 'post_stock');
        });
    }
}
