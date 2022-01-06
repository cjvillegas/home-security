<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickingUrlColumnInStockOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_orders', function (Blueprint $table) {
            $table->tinyText('picking_url')->nullable()->after('sage_order_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_orders', function (Blueprint $table) {
            $table->dropColumn('picking_url');
        });
    }
}
