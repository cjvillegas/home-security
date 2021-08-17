<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockFieldsOnStockLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->float('available_stock', 8, 2)->change();
            $table->float('po_stock', 8, 2)->change();
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
            //
        });
    }
}
