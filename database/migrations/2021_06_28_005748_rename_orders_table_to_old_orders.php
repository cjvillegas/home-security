<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOrdersTableToOldOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('orders')) {
            Schema::rename('orders', 'old_orders');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop the new orders table
        Schema::dropIfExists('orders');

        // rename the old orders table back to orders
        if (Schema::hasTable('old_orders')) {
            Schema::rename('old_orders', 'orders');
        }
    }
}
