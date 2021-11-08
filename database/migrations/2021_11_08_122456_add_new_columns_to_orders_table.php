<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('account_code')->nullable()->after('product_type');
            $table->decimal('width', 10, 2)->nullable()->after('account_code');
            $table->decimal('drop', 10, 2)->nullable()->after('width');
            $table->string('stock_code')->nullable()->after('drop');
            $table->string('fabric_range')->nullable()->after('stock_code');
            $table->string('color')->nullable()->after('fabric_range');
            $table->decimal('item_price', 10, 2)->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('account_code');
            $table->dropColumn('width');
            $table->dropColumn('drop');
            $table->dropColumn('stock_code');
            $table->dropColumn('fabric_range');
            $table->dropColumn('color');
            $table->dropColumn('item_price');
        });
    }
}
