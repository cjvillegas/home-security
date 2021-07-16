<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('serial_id', 'orders_serial_id_index');
        });

        Schema::table('scanners', function (Blueprint $table) {
            $table->index('blindid', 'scanners_blindid_index');
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
            if ($doctrineTable->hasIndex('orders_serial_id_index')) {
                $table->dropIndex('orders_serial_id_index');
            }
        });

        Schema::table('scanners', function (Blueprint $table) use ($sm) {
            $doctrineTable = $sm->listTableDetails('scanners');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('scanners_blindid_index')) {
                $table->dropIndex('scanners_blindid_index');
            }
        });
    }
}
