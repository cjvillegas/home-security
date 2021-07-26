<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdInOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('category_id')->nullable()->after('serial_id');

            $table->index('category_id', 'orders_category_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        Schema::table('orders', function (Blueprint $table) use ($sm) {
            $doctrineTable = $sm->listTableDetails('orders');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('orders_category_id_index')) {
                $table->dropIndex('orders_category_id_index');
            }

            $table->dropColumn('category_id');
        });
    }
}
